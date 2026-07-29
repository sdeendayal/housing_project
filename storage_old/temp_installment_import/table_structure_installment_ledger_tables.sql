-- ============================================================
-- TABLE STRUCTURES
-- Generated from uploaded data files
-- ============================================================
 
-- 10. Installment Due
CREATE TABLE installment_due (
    DueInstallmentId        INT             NOT NULL PRIMARY KEY,
    PropertyAuctionId       INT             NOT NULL,
    AssetId                 INT             NOT NULL,
    OfferOfPossessionDate   DATE            NULL,
    InstallmentNumber       INT             NOT NULL,
    DueDate                 DATE            NOT NULL,
    RunningBalance          INT             NOT NULL DEFAULT 0,
    EMIAmount               INT             NOT NULL DEFAULT 0,
    PrincipleAmount         INT             NOT NULL DEFAULT 0,
    InterestAmount          INT             NOT NULL DEFAULT 0,
    GSTAmount               INT             NOT NULL DEFAULT 0,
    InsuranceAmout          INT             NOT NULL DEFAULT 0,
    DueAmount               DECIMAL(15,2)   NOT NULL DEFAULT 0,
    RunningClosingBalance   INT             NOT NULL DEFAULT 0,
    LastSettledDate         DATE            NULL,
    CompanyId               INT             NOT NULL,
    CreatedDate             DATETIME        NULL,
    CreatedBy               INT             NULL,
    ModifiedDate            DATETIME        NULL,
    ModifiedBy              INT             NULL,
    IsDeleted               TINYINT(1)      NOT NULL DEFAULT 0,
    IsActive                TINYINT(1)      NOT NULL DEFAULT 1,
    InstallmentPhase        FLOAT           NULL,
    PrincipalBalance        FLOAT           NULL,
 
    FOREIGN KEY (PropertyAuctionId) REFERENCES property_auction_detail(PropertyAuctionId),
    FOREIGN KEY (AssetId)           REFERENCES property_registration(AssetId)
);
 
-- ============================================================
 
-- 11. Ledger
CREATE TABLE ledger (
    Id                          INT             NOT NULL PRIMARY KEY,
    InstallmentNumber           INT             NOT NULL,
    DueDate                     DATE            NOT NULL,
    PrincipalAmount             INT             NOT NULL DEFAULT 0,
    InterestAmount              INT             NOT NULL DEFAULT 0,
    GSTAmount                   INT             NOT NULL DEFAULT 0,
    InsuranceAmount             INT             NOT NULL DEFAULT 0,
    EMIAmount                   INT             NOT NULL DEFAULT 0,
    CalculatedAmount            INT             NOT NULL DEFAULT 0,
    PenaltyAmount               INT             NOT NULL DEFAULT 0,
    PenaltyRate                 INT             NOT NULL DEFAULT 0,
    GSTonPenalty                INT             NOT NULL DEFAULT 0,
    Payment                     INT             NOT NULL DEFAULT 0,
    CumulativePenalty           INT             NOT NULL DEFAULT 0,
    CumulativeGST               INT             NOT NULL DEFAULT 0,
    RemainingBalance            INT             NOT NULL DEFAULT 0,
    ConsecutiveMissedPayments   INT             NOT NULL DEFAULT 0,
    Payable_amount              INT             NOT NULL DEFAULT 0,
    total_gst                   INT             NOT NULL DEFAULT 0,
    gst_running_bal             INT             NOT NULL DEFAULT 0,
    int_on_gst                  INT             NOT NULL DEFAULT 0,
    int_running_bal             INT             NOT NULL DEFAULT 0,
    total_gst_int_payable       INT             NOT NULL DEFAULT 0,
    gst_payment                 INT             NOT NULL DEFAULT 0,
    balance_amount              INT             NOT NULL DEFAULT 0,
    CompanyId                   INT             NOT NULL,
    Is_Active                   TINYINT(1)      NOT NULL DEFAULT 1,
    Is_Deleted                  TINYINT(1)      NOT NULL DEFAULT 0,
    CreatedBy                   INT             NULL,
    CreateDate                  DATETIME        NULL,
    AuthorizedBy                INT             NULL,
    AuthorizedDate              DATETIME        NULL,
    AssetId                     INT             NOT NULL,
    PaneltyOnAmount             FLOAT           NULL,
    InstallmentPhase            FLOAT           NULL,
    PrincipalBalance            FLOAT           NULL,
 
    FOREIGN KEY (AssetId) REFERENCES property_registration(AssetId)
);