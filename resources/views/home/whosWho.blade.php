@extends('layouts.app')

@section('title', 'About Us - Who\'s Who')

@section('content')

    {{-- Main Content --}}
    <main class="lg:col-span-2">
        <section class="bg-[#eef3f9] py-8">
            <div class="max-w-7xl mx-auto px-4">

                <!-- Heading -->
                <div class="text-center mb-8">

                    <div
                        class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-5 py-2 rounded-full text-[12px] font-semibold shadow-sm">

                        <span class="material-symbols-outlined text-[17px]">
                            groups
                        </span>

                        Who's Who

                    </div>

                </div>

                <!-- Card -->
                <div class="bg-white rounded-[26px] shadow-lg border border-slate-200 px-6 py-6">

                    <div class="overflow-x-auto">

                        <table id="whoTable" class="w-full text-[13px] text-left border-collapse">

                            <!-- Header -->
                            <thead>

                                <tr class="bg-gradient-to-r from-[#0f75c8] to-[#0b3c74] text-white">

                                    <th class="px-4 py-4 font-semibold rounded-tl-2xl">
                                        Sr.<br>No.
                                    </th>

                                    <th class="px-4 py-4 font-semibold">
                                        Name of Officer / Official
                                    </th>

                                    <th class="px-4 py-4 font-semibold">
                                        Designation
                                    </th>

                                    <th class="px-4 py-4 font-semibold">
                                        Mobile No.
                                    </th>

                                    <th class="px-4 py-4 font-semibold">
                                        Tel(O)
                                    </th>

                                    <th class="px-4 py-4 font-semibold rounded-tr-2xl">
                                        Email ID
                                    </th>

                                </tr>

                            </thead>

                            <!-- Body -->
                            <tbody class="text-slate-700">

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td class="px-4 py-4 font-medium">1</td>
                                    <td class="px-4 py-4 font-medium">Sh. Nayab Singh Saini</td>
                                    <td class="px-4 py-4">Hon’ble Chief Minister</td>
                                    <td class="px-4 py-4">-</td>
                                    <td class="px-4 py-4">0172-2749396 / 2749409</td>
                                    <td class="px-4 py-4 text-blue-700">cmharyana@nic.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td class="px-4 py-4 font-medium">2</td>
                                    <td class="px-4 py-4">-</td>
                                    <td class="px-4 py-4">-</td>
                                    <td class="px-4 py-4">-</td>
                                    <td class="px-4 py-4">-</td>
                                    <td class="px-4 py-4">-</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td class="px-4 py-4 font-medium">3</td>
                                    <td class="px-4 py-4 font-medium">Sh. Mohammad Shayin, IAS</td>
                                    <td class="px-4 py-4">Commissioner & Secretary to Govt. Haryana</td>
                                    <td class="px-4 py-4">-</td>
                                    <td class="px-4 py-4">0172-5022402</td>
                                    <td class="px-4 py-4 text-blue-700">commissionersecretaryhfa@gmail.com</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td class="px-4 py-4 font-medium">4</td>
                                    <td class="px-4 py-4 font-medium">Sh. Dinesh Kumar</td>
                                    <td class="px-4 py-4">PS/C&S</td>
                                    <td class="px-4 py-4">-</td>
                                    <td class="px-4 py-4">0172-5022402</td>
                                    <td class="px-4 py-4 text-blue-700">md@hpgcl.org.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td class="px-4 py-4 font-medium">5</td>
                                    <td class="px-4 py-4 font-medium">Sh. J. Ganesan, IAS</td>
                                    <td class="px-4 py-4">Director General</td>
                                    <td class="px-4 py-4">-</td>
                                    <td class="px-4 py-4">0172-2568006, 0172-2568005 Fax</td>
                                    <td class="px-4 py-4 text-blue-700">admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td class="px-4 py-4 font-medium">6</td>
                                    <td class="px-4 py-4 font-medium">Sh. Roop Kishore and Smt. Nancy</td>
                                    <td class="px-4 py-4">PA to Director General</td>
                                    <td class="px-4 py-4">-</td>
                                    <td class="px-4 py-4">0172-2568006</td>
                                    <td class="px-4 py-4 text-blue-700">admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td class="px-4 py-4 font-medium">7</td>
                                    <td class="px-4 py-4 font-medium">Smt. Ruchi Singh Bedi, HCS</td>
                                    <td class="px-4 py-4">Additional Director</td>
                                    <td class="px-4 py-4">-</td>
                                    <td class="px-4 py-4">0172-2578288</td>
                                    <td class="px-4 py-4 text-blue-700">admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td class="px-4 py-4 font-medium">8</td>
                                    <td class="px-4 py-4 font-medium">Smt. Rajni Sharma</td>
                                    <td class="px-4 py-4">PA to Additional Director</td>
                                    <td class="px-4 py-4">-</td>
                                    <td class="px-4 py-4">0172-2578288</td>
                                    <td class="px-4 py-4 text-blue-700">admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td class="px-4 py-4 font-medium">9</td>
                                    <td class="px-4 py-4 font-medium">Sh. Lalit Kumar</td>
                                    <td class="px-4 py-4">Accounts Officer</td>
                                    <td class="px-4 py-4">8901776677</td>
                                    <td class="px-4 py-4">0172-2568006</td>
                                    <td class="px-4 py-4 text-blue-700">admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td>10</td>
                                    <td>Sh. Aman Godara</td>
                                    <td>Assistant Town Planner</td>
                                    <td>-</td>
                                    <td>0172-2568006</td>
                                    <td>admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td>11</td>
                                    <td>Sh. Mahender Singh</td>
                                    <td>State Urban Economist (PMAY-U)</td>
                                    <td>9464741686</td>
                                    <td>0172-2568006</td>
                                    <td>admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td>12</td>
                                    <td>Sh. Harpreet Singh</td>
                                    <td>State Municipal Finance (PMAY-U)</td>
                                    <td>8901003521</td>
                                    <td>0172-2568006</td>
                                    <td>admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td>13</td>
                                    <td>Sh. Sandeep Kumar</td>
                                    <td>State Municipal Civil Engineer (PMAY-U)</td>
                                    <td>9466029111</td>
                                    <td>0172-2568006</td>
                                    <td>admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td>14</td>
                                    <td>Ms. Vinita</td>
                                    <td>State MIS (PMAY-U)</td>
                                    <td>8146071337</td>
                                    <td>0172-2568006</td>
                                    <td>admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td>15</td>
                                    <td>Smt. Seema Sharma</td>
                                    <td>CBT Specialist (PMAY-U)</td>
                                    <td>9988403111</td>
                                    <td>0172-2568006</td>
                                    <td>admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td>16</td>
                                    <td>Sh. Devender Singh</td>
                                    <td>State Co-ordinator (PMAY-G)</td>
                                    <td>9417900220</td>
                                    <td>0172-2568006</td>
                                    <td>admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="border-b border-slate-100 hover:bg-blue-50 transition">
                                    <td>17</td>
                                    <td>Smt. Nancy</td>
                                    <td>State MIS (PMAY-G)</td>
                                    <td>-</td>
                                    <td>0172-2568006</td>
                                    <td>admin-hfa@hry.gov.in</td>
                                </tr>

                                <tr class="hover:bg-blue-50 transition">
                                    <td>18</td>
                                    <td>Ms. Raveena</td>
                                    <td>State Finance Expert (PMAY-G)</td>
                                    <td>-</td>
                                    <td>0172-2568006</td>
                                    <td>admin-hfa@hry.gov.in</td>
                                </tr>

                            </tbody>

                        </table>

                    </div>
                </div>
            </div>
        </section>
    </main>
    {{-- Right Sidebar --}}
    @include('partials.rightSidebar')
@endsection
