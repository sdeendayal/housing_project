import os
import sys
import time
import zipfile
import gzip
import csv
import io
import datetime
import xml.etree.ElementTree as ET

def convert(base_dir=None):
    if base_dir is None:
        base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__))) + '/data/payment_detail_hfa'

    zip_candidates = [
        os.path.join(base_dir, 'payment detail HFA.zip'),
        os.path.join(base_dir, 'payment_detail_hfa.zip'),
        os.path.dirname(base_dir) + '/payment detail HFA.zip',
    ]

    zip_path = None
    for p in zip_candidates:
        if os.path.exists(p):
            zip_path = p
            break

    if not zip_path:
        print(f"Error: No payment detail HFA.zip found in {base_dir}")
        return False

    out_dir = base_dir
    os.makedirs(out_dir, exist_ok=True)

    print(f"Opening {zip_path}...")
    start_time = time.time()

    def excel_to_date_str(val):
        if not val or val == 'NULL' or val == '':
            return None
        try:
            f = float(val)
            d = datetime.datetime(1899, 12, 30) + datetime.timedelta(days=f)
            if f == int(f):
                return d.strftime('%Y-%m-%d')
            else:
                return d.strftime('%Y-%m-%d %H:%M:%S')
        except Exception:
            return val

    with zipfile.ZipFile(zip_path, 'r') as outer_zip:
        xlsx_names = [n for n in outer_zip.namelist() if n.endswith('.xlsx')]
        if not xlsx_names:
            print("No .xlsx file found in zip")
            return False
        xlsx_name = xlsx_names[0]
        print(f"Extracting xlsx stream: {xlsx_name}")
        xlsx_bytes = outer_zip.read(xlsx_name)

    with zipfile.ZipFile(io.BytesIO(xlsx_bytes), 'r') as z:
        ns = '{http://schemas.openxmlformats.org/spreadsheetml/2006/main}'

        print("Loading sharedStrings...")
        sst = []
        if 'xl/sharedStrings.xml' in z.namelist():
            sst_root = ET.fromstring(z.read('xl/sharedStrings.xml'))
            for si in sst_root.findall(f'{ns}si'):
                t = si.find(f'{ns}t')
                if t is not None and t.text:
                    sst.append(t.text)
                else:
                    text = ''.join(e.text for e in si.findall(f'.//{ns}t') if e.text)
                    sst.append(text)
        print(f"Loaded {len(sst)} shared strings in {time.time() - start_time:.2f}s")

        def col_to_idx(col):
            idx = 0
            for c in col:
                idx = idx * 26 + (ord(c) - 64)
            return idx - 1

        def process_sheet(sheet_xml_path, out_gz_path, column_formatter):
            t0 = time.time()
            print(f"Processing {sheet_xml_path} -> {out_gz_path}...")

            with z.open(sheet_xml_path) as f, gzip.open(out_gz_path, 'wt', encoding='utf-8', newline='') as out_f:
                writer = csv.writer(out_f)
                header_written = False
                header_map = {}
                row_count = 0

                for event, elem in ET.iterparse(f, events=('end',)):
                    if elem.tag == f'{ns}row':
                        r_idx = elem.attrib.get('r')
                        cells = {}
                        for c in elem.findall(f'{ns}c'):
                            ref = c.attrib.get('r', '')
                            cell_type = c.attrib.get('t')
                            val_elem = c.find(f'{ns}v')
                            val = val_elem.text if val_elem is not None else ''
                            if cell_type == 's' and val:
                                val = sst[int(val)]

                            col_letter = ''.join(ch for ch in ref if ch.isalpha())
                            if col_letter:
                                cells[col_to_idx(col_letter)] = val

                        if not cells:
                            elem.clear()
                            continue

                        max_col = max(cells.keys())
                        row_data = [cells.get(i, '') for i in range(max_col + 1)]

                        if not header_written:
                            header = row_data
                            header_map = {name.strip(): idx for idx, name in enumerate(header)}
                            out_header = column_formatter('header', header_map, None)
                            writer.writerow(out_header)
                            header_written = True
                        else:
                            out_row = column_formatter('row', header_map, row_data)
                            if out_row is not None:
                                writer.writerow(out_row)
                                row_count += 1
                                if row_count % 100000 == 0:
                                    print(f"  ... {row_count} rows written")

                        elem.clear()
            print(f"Completed {sheet_xml_path}: {row_count} rows in {time.time() - t0:.2f}s")

        # 1. cashreceipt -> cash_receipt_details
        def format_cashreceipt(mode, hmap, row):
            if mode == 'header':
                return ['id','asset_number','total_paid_amount','receipt_number','BranchId','DistrictId','CityId','SectorId','IsActive','IsDeleted','created_date','CreatedBy','ModifiedDate','ModifiedBy','CompanyId']

            def g(col_name):
                idx = hmap.get(col_name)
                if idx is not None and idx < len(row):
                    return row[idx]
                return ''

            cid = g('id')
            if not cid:
                return None

            cdate = excel_to_date_str(g('created_date'))
            mdate = excel_to_date_str(g('modified_date'))

            return [
                cid,
                g('asset_number'),
                g('total_paid_amount'),
                g('receipt_number'),
                g('estate_manager_office'), # BranchId
                g('district_office'),       # DistrictId
                g('city_office'),           # CityId
                g('sector'),                # SectorId
                g('IsActive') if g('IsActive') != '' else '1',
                g('IsDeleted') if g('IsDeleted') != '' else '0',
                cdate or '',
                g('createdBy'),
                mdate or '',
                g('modifiedBy'),
                g('companyId') if g('companyId') else '544'
            ]

        # 2. Dueinstallments -> installment_due
        def format_installments(mode, hmap, row):
            if mode == 'header':
                return ['DueInstallmentId','PropertyAuctionId','AssetId','OfferOfPossessionDate','InstallmentNumber','DueDate','RunningBalance','EMIAmount','PrincipleAmount','InterestAmount','GSTAmount','InsuranceAmout','DueAmount','RunningClosingBalance','LastSettledDate','CompanyId','CreatedDate','CreatedBy','ModifiedDate','ModifiedBy','IsDeleted','IsActive','InstallmentPhase','PrincipalBalance']

            def g(col_name):
                idx = hmap.get(col_name)
                if idx is not None and idx < len(row):
                    return row[idx]
                return ''

            iid = g('DueInstallmentId')
            if not iid:
                return None

            oop_date = excel_to_date_str(g('OfferOfPossessionDate'))
            due_date = excel_to_date_str(g('DueDate'))
            last_settled = excel_to_date_str(g('LastSettledDate'))
            cdate = excel_to_date_str(g('CreatedDate'))
            mdate = excel_to_date_str(g('ModifiedDate'))

            return [
                iid,
                g('PropertyAuctionId'),
                g('AssetId'),
                oop_date or '',
                g('InstallmentNumber'),
                due_date or '',
                g('RunningBalance'),
                g('EMIAmount'),
                g('PrincipleAmount'),
                g('InterestAmount'),
                g('GSTAmount'),
                g('InsuranceAmout'),
                g('DueAmount'),
                g('RunningClosingBalance'),
                last_settled or '',
                g('CompanyId') if g('CompanyId') else '544',
                cdate or '',
                g('CreatedBy'),
                mdate or '',
                g('ModifiedBy'),
                g('IsDeleted') if g('IsDeleted') != '' else '0',
                g('IsActive') if g('IsActive') != '' else '1',
                g('InstallmentPhase'),
                g('PrincipalBalance')
            ]

        # 3. ledger detail -> ledger
        def format_ledger(mode, hmap, row):
            if mode == 'header':
                return ['Id','InstallmentNumber','DueDate','PrincipalAmount','InterestAmount','GSTAmount','InsuranceAmount','EMIAmount','CalculatedAmount','PenaltyAmount','PenaltyRate','GSTonPenalty','Payment','CumulativePenalty','CumulativeGST','RemainingBalance','ConsecutiveMissedPayments','Payable_amount','total_gst','gst_running_bal','int_on_gst','int_running_bal','total_gst_int_payable','gst_payment','balance_amount','CompanyId','Is_Active','Is_Deleted','CreatedBy','CreateDate','AuthorizedBy','AuthorizedDate','AssetId','PaneltyOnAmount','InstallmentPhase','PrincipalBalance']

            def g(col_name):
                idx = hmap.get(col_name)
                if idx is not None and idx < len(row):
                    return row[idx]
                return ''

            lid = g('Id')
            if not lid:
                return None

            due_date = excel_to_date_str(g('DueDate'))
            cdate = excel_to_date_str(g('CreateDate'))
            adate = excel_to_date_str(g('AuthorizedDate'))

            return [
                lid,
                g('InstallmentNumber'),
                due_date or '',
                g('PrincipalAmount'),
                g('InterestAmount'),
                g('GSTAmount'),
                g('InsuranceAmount'),
                g('EMIAmount'),
                g('CalculatedAmount'),
                g('PenaltyAmount'),
                g('PenaltyRate'),
                g('GSTonPenalty'),
                g('Payment'),
                g('CumulativePenalty'),
                g('CumulativeGST'),
                g('RemainingBalance'),
                g('ConsecutiveMissedPayments'),
                g('Payable_amount'),
                g('total_gst'),
                g('gst_running_bal'),
                g('int_on_gst'),
                g('int_running_bal'),
                g('total_gst_int_payable'),
                g('gst_payment'),
                g('balance_amount'),
                g('CompanyId') if g('CompanyId') else '544',
                g('Is_Active') if g('Is_Active') != '' else '1',
                g('Is_Deleted') if g('Is_Deleted') != '' else '0',
                g('CreatedBy'),
                cdate or '',
                g('AuthorizedBy'),
                adate or '',
                g('AssetId'),
                g('PaneltyOnAmount'),
                g('InstallmentPhase'),
                g('PrincipalBalance')
            ]

        process_sheet('xl/worksheets/sheet3.xml', f'{out_dir}/cash_receipt_details.csv.gz', format_cashreceipt)
        process_sheet('xl/worksheets/sheet1.xml', f'{out_dir}/installment_due.csv.gz', format_installments)
        process_sheet('xl/worksheets/sheet2.xml', f'{out_dir}/ledger.csv.gz', format_ledger)

    print(f"All done in {time.time() - start_time:.2f}s!")
    return True

if __name__ == '__main__':
    target_dir = sys.argv[1] if len(sys.argv) > 1 else None
    success = convert(target_dir)
    sys.exit(0 if success else 1)
