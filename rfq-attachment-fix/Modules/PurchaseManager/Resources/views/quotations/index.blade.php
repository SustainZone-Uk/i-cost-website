@extends('user::layouts.masterlist')

@section('content')

<style>
    /*
    |--------------------------------------------------------------------------
    | iCost Requests for Quotation Page
    |--------------------------------------------------------------------------
    */

    .icost-page {
        padding: 10px 15px 30px 15px;
    }

    .icost-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .icost-page-title {
        margin: 0;
        font-size: 34px;
        font-weight: 700;
        color: #102a43;
        line-height: 1.2;
    }

    .icost-page-subtitle {
        margin-top: 6px;
        margin-bottom: 0;
        font-size: 15px;
        color: #7b8794;
    }

    .icost-page-date {
        color: #7b8794;
        font-size: 14px;
        padding-top: 8px;
        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | Summary Cards
    |--------------------------------------------------------------------------
    */

    .quotation-summary-row {
        margin-bottom: 22px;
    }

    .quotation-summary-card {
        background:
            linear-gradient(
                110deg,
                #ffffff 0%,
                #f8fff9 100%
            );
        border: 1px solid #d9ebe0;
        border-radius: 16px;
        min-height: 145px;
        padding: 24px 22px;
        display: flex;
        align-items: flex-start;
        box-shadow: 0 5px 18px rgba(15, 39, 64, 0.025);
        height: 100%;
    }

    .quotation-summary-icon {
        width: 54px;
        height: 54px;
        min-width: 54px;
        border-radius: 14px;
        background: #e8f6df;
        color: #28a745;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 23px;
        margin-right: 16px;
    }

    .quotation-summary-content {
        min-width: 0;
    }

    .quotation-summary-label {
        color: #344e68;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .quotation-summary-value {
        color: #0f2740;
        font-size: 29px;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 6px;
    }

    .quotation-summary-value.project-value {
        font-size: 21px;
        line-height: 1.35;
        padding-top: 3px;
        word-break: break-word;
    }

    .quotation-summary-description {
        color: #8492a6;
        font-size: 13px;
    }


    /*
    |--------------------------------------------------------------------------
    | Main Quotations Card
    |--------------------------------------------------------------------------
    */

    .quotation-main-card {
        background: #ffffff;
        border: 1px solid #e0e7ee;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(15, 39, 64, 0.06);
    }

    .quotation-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 22px;
        border-bottom: 1px solid #e8edf2;
        background: #ffffff;
    }

    .quotation-title-wrapper {
        display: flex;
        align-items: center;
        min-width: 0;
    }

    .quotation-card-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 13px;
        background: #eef4f8;
        color: #123d62;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
        font-size: 20px;
    }

    .quotation-card-title {
        color: #102a43;
        font-size: 21px;
        font-weight: 700;
        line-height: 1.2;
        margin: 0;
    }

    .quotation-card-subtitle {
        color: #8492a6;
        font-size: 13px;
        margin-top: 4px;
    }


    /*
    |--------------------------------------------------------------------------
    | Create RFQ Button
    |--------------------------------------------------------------------------
    */

    .quotation-create-button {
        border: 0;
        outline: none;
        background: #32bf64;
        color: #ffffff !important;
        min-height: 46px;
        border-radius: 9px;
        padding: 0 20px;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: none;
        transition: 0.2s ease;
        text-decoration: none !important;
    }

    .quotation-create-button:hover {
        background: #27ad57;
        color: #ffffff !important;
        transform: translateY(-1px);
    }

    .quotation-create-button i {
        font-size: 14px;
        margin-right: 9px;
    }


    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    .quotation-toolbar {
        padding: 20px 22px;
        border-bottom: 1px solid #edf1f5;
    }

    .quotation-search-wrapper {
        position: relative;
    }

    .quotation-search-icon {
        position: absolute;
        left: 17px;
        top: 50%;
        transform: translateY(-50%);
        color: #718096;
        z-index: 2;
        font-size: 14px;
    }

    .quotation-search-input {
        width: 100%;
        height: 46px;
        border: 1px solid #d8e1e9;
        border-radius: 10px;
        outline: none;
        padding: 0 16px 0 45px;
        font-size: 14px;
        color: #344e68;
        background: #ffffff;
        transition: 0.2s ease;
    }

    .quotation-search-input::placeholder {
        color: #909eac;
    }

    .quotation-search-input:focus {
        border-color: #8acda0;
        box-shadow: 0 0 0 3px rgba(50, 191, 100, 0.08);
    }

    .quotation-project-label {
        color: #52667a;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 7px;
        display: block;
    }

    .quotation-toolbar .form-control {
        height: 46px;
        border-radius: 10px;
        border: 1px solid #d8e1e9;
        color: #344e68;
        font-size: 14px;
        box-shadow: none;
    }

    .quotation-toolbar .form-control:focus {
        border-color: #8acda0;
        box-shadow: 0 0 0 3px rgba(50, 191, 100, 0.08);
    }


    /*
    |--------------------------------------------------------------------------
    | Table
    |--------------------------------------------------------------------------
    */

    .quotation-table-area {
        padding: 0 22px 18px 22px;
    }

    #quotations-datatable {
        width: 100% !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0;
        border: 1px solid #e4eaf0;
    }

    #quotations-datatable thead th {
        background: #f8fafc;
        color: #425466;
        font-size: 12px;
        font-weight: 700;
        padding: 15px 13px;
        border-top: 0;
        border-bottom: 1px solid #dfe6ec;
        border-right: 1px solid #e8edf2;
        vertical-align: middle;
        white-space: nowrap;
    }

    #quotations-datatable thead th:last-child {
        border-right: 0;
    }

    #quotations-datatable tbody td {
        padding: 15px 13px;
        vertical-align: middle;
        color: #425466;
        font-size: 13px;
        border-top: 0;
        border-bottom: 1px solid #e9eef3;
        border-right: 1px solid #edf1f5;
        background: #ffffff;
    }

    #quotations-datatable tbody td:last-child {
        border-right: 0;
    }

    #quotations-datatable tbody tr:last-child td {
        border-bottom: 0;
    }

    #quotations-datatable tbody tr:hover td {
        background: #fbfdfb;
    }


    /*
    |--------------------------------------------------------------------------
    | RFQ Number Badge
    |--------------------------------------------------------------------------
    */

    .rfq-number-badge {
        display: inline-flex;
        align-items: center;
        background: #eef4f8;
        border: 1px solid #d8e2ea;
        border-radius: 8px;
        color: #28445e;
        font-weight: 700;
        padding: 5px 10px;
        font-size: 12px;
        white-space: nowrap;
    }


    /*
    |--------------------------------------------------------------------------
    | DataTables Controls
    |--------------------------------------------------------------------------
    */

    .dataTables_wrapper {
        padding-top: 20px;
    }

    .dataTables_wrapper .dataTables_info {
        color: #60758a;
        font-size: 13px;
        padding-top: 18px !important;
    }

    .dataTables_wrapper .dataTables_paginate {
        padding-top: 12px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border: 1px solid #dce4eb !important;
        background: #ffffff !important;
        color: #53677a !important;
        border-radius: 0 !important;
        padding: 8px 13px !important;
        margin-left: 0 !important;
        box-shadow: none !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:first-child {
        border-radius: 8px 0 0 8px !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:last-child {
        border-radius: 0 8px 8px 0 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #2f70d8 !important;
        border-color: #2f70d8 !important;
        color: #ffffff !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f1f5f9 !important;
        border-color: #d5dde5 !important;
        color: #243b53 !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
        color: #9ca9b5 !important;
        opacity: 0.7;
    }

    .dataTables_length {
        display: none;
    }

    .dataTables_filter {
        display: none;
    }


    /*
    |--------------------------------------------------------------------------
    | Action Buttons
    |--------------------------------------------------------------------------
    */

    #quotations-datatable td:last-child .btn {
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 4px;
        border: 0;
        box-shadow: none;
    }

    #quotations-datatable td:last-child .btn-success {
        background: #49b95f;
    }

    #quotations-datatable td:last-child .btn-primary,
    #quotations-datatable td:last-child .btn-info {
        background: #169beb;
    }

    #quotations-datatable td:last-child .btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }


    /*
    |--------------------------------------------------------------------------
    | Processing
    |--------------------------------------------------------------------------
    */

    div.dataTables_processing {
        border: 1px solid #e1e8ee !important;
        border-radius: 10px;
        background: #ffffff !important;
        color: #344e68 !important;
        box-shadow: 0 8px 25px rgba(15, 39, 64, 0.08);
    }


    /*
    |--------------------------------------------------------------------------
    | Responsive
    |--------------------------------------------------------------------------
    */

    @media (max-width: 991px) {

        .quotation-summary-card {
            margin-bottom: 15px;
        }

        .quotation-project-filter {
            margin-top: 15px;
        }

        .quotation-card-header {
            align-items: flex-start;
        }

        .quotation-create-button {
            margin-left: 15px;
        }
    }

    @media (max-width: 767px) {

        .icost-page {
            padding: 5px 5px 25px 5px;
        }

        .icost-page-header {
            display: block;
        }

        .icost-page-date {
            margin-top: 8px;
        }

        .icost-page-title {
            font-size: 28px;
        }

        .quotation-card-header {
            display: block;
        }

        .quotation-create-button {
            margin-top: 16px;
            margin-left: 0;
            width: 100%;
        }

        .quotation-table-area {
            overflow-x: auto;
            padding: 0 15px 18px 15px;
        }

        .quotation-toolbar {
            padding: 16px 15px;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Dashboard look (matches home.blade.php): navy banner, KPI cards with a
    | coloured base line, gradient panel header with an icon badge.
    |--------------------------------------------------------------------------
    */

    .icost-page {
        padding: 4px 0 30px 0;
    }

    .icost-page-header {
        align-items: center;
        gap: 20px;
        margin-bottom: 12px;
        padding: 18px 22px;
        overflow: hidden;
        border-radius: 16px;
        background:
            radial-gradient(circle at 73% 10%, rgba(255,255,255,.15), transparent 24%),
            radial-gradient(circle at 88% 118%, rgba(27, 164, 188, .26), transparent 27%),
            linear-gradient(110deg, #071a2d 0%, #0b2340 46%, #174d72 72%, #2a6b83 100%);
        box-shadow: 0 12px 30px rgba(7, 26, 45, .18);
    }

    .page-crumbs {
        display: flex;
        gap: 6px;
        margin-bottom: 6px;
        color: rgba(236, 247, 255, .6);
        font-size: 11px;
    }

    .page-crumbs a { color: #8edbff; }
    .page-crumbs a:hover { color: #ffffff; text-decoration: none; }

    .icost-page-title {
        color: #ffffff;
        font-size: 30px;
        font-weight: 800;
        line-height: 1.08;
        letter-spacing: -0.025em;
    }

    .icost-page-subtitle {
        margin-top: 5px;
        color: rgba(236, 247, 255, .78);
        font-size: 13px;
    }

    .icost-page-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 34px;
        padding: 0 12px;
        border: 1px solid rgba(255,255,255,.18);
        border-radius: 10px;
        background: rgba(255,255,255,.08);
        color: rgba(255,255,255,.88);
        font-size: 12px;
    }

    .icost-page-date i { color: #8edbff; }

    .quotation-summary-row {
        margin-left: -6px;
        margin-right: -6px;
        margin-bottom: 12px;
    }

    .quotation-summary-row > [class*="col-"] {
        padding-left: 6px;
        padding-right: 6px;
    }

    .quotation-summary-card {
        min-height: 122px;
        padding: 17px;
        flex-direction: row-reverse;
        justify-content: space-between;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #d4e2ec;
        border-bottom: 4px solid #1687d9;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(17, 52, 80, .07);
    }

    .quotation-summary-row > div:nth-child(2) .quotation-summary-card { border-bottom-color: #20a66a; }
    .quotation-summary-row > div:nth-child(3) .quotation-summary-card { border-bottom-color: #6474d9; }

    .quotation-summary-icon {
        width: 46px;
        height: 46px;
        min-width: 46px;
        margin-right: 0;
        margin-left: 13px;
        border-radius: 12px;
        font-size: 18px;
        background: #e8f3fc;
        color: #1687d9;
        box-shadow: 0 0 0 28px rgba(22, 135, 217, .05);
    }

    .quotation-summary-row > div:nth-child(2) .quotation-summary-icon {
        background: #e9f7f0;
        color: #20a66a;
        box-shadow: 0 0 0 28px rgba(32, 166, 106, .05);
    }

    .quotation-summary-row > div:nth-child(3) .quotation-summary-icon {
        background: #eef0ff;
        color: #6474d9;
        box-shadow: 0 0 0 28px rgba(100, 116, 217, .05);
    }

    .quotation-summary-label {
        color: #0b2340;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .quotation-summary-value {
        margin: 6px 0 8px;
        color: #0b2340;
        font-size: 28px;
        font-weight: 800;
    }

    .quotation-summary-value.project-value {
        font-size: 20px;
    }

    .quotation-summary-description {
        color: #6b7f92;
        font-size: 11px;
    }

    .quotation-main-card {
        border: 1px solid #d4e2ec;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(17, 52, 80, .07);
    }

    .quotation-card-header {
        padding: 11px 16px 10px;
        border-bottom: 1px solid #e5edf4;
        background: linear-gradient(90deg, #f8fcff 0%, #eef7fb 65%, #eef9f5 100%);
    }

    .quotation-card-icon {
        width: 36px;
        height: 36px;
        min-width: 36px;
        margin-right: 11px;
        border-radius: 11px;
        background: linear-gradient(145deg, #22b978, #168c60);
        color: #ffffff;
        font-size: 14px;
        box-shadow: 0 7px 16px rgba(32, 166, 106, .19);
    }

    .quotation-card-title {
        color: #0b2340;
        font-size: 17px;
        font-weight: 800;
    }

    .quotation-card-subtitle {
        margin-top: 2px;
        color: #6b7f92;
        font-size: 11px;
    }

    .quotation-create-button {
        min-height: 40px;
        padding: 0 16px;
        border-radius: 9px;
        background: #20a66a;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 7px 16px rgba(32, 166, 106, .25);
    }

    .quotation-create-button:hover {
        background: #168c60;
    }

    .quotation-toolbar {
        padding: 16px 16px 14px;
        border-bottom: 1px solid #e5edf4;
    }

    .quotation-project-label {
        color: #35506a;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .055em;
        margin-bottom: 6px;
    }

    .quotation-search-input,
    .quotation-toolbar .form-control {
        height: 41px;
        border-color: #c9d9e6;
        border-radius: 9px;
        color: #193c59;
        font-size: 12px;
    }

    .quotation-search-input:focus,
    .quotation-toolbar .form-control:focus {
        border-color: #5faee4;
        box-shadow: 0 0 0 3px rgba(22, 135, 217, .10);
    }

    .quotation-search-icon {
        color: #94a3b8;
        font-size: 13px;
    }

    .quotation-table-area {
        padding: 16px 16px 16px;
    }

    #quotations-datatable {
        border-color: #d4e2ec;
        border-radius: 10px;
        overflow: hidden;
    }

    #quotations-datatable thead th {
        background: #f4f8fb;
        color: #35506a;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .055em;
        padding: 12px 13px;
    }

    #quotations-datatable tbody td {
        color: #193c59;
        font-size: 12px;
        padding: 13px;
    }

    .dataTables_wrapper {
        padding-top: 0;
    }

    .dataTables_wrapper .dataTables_info {
        color: #6b7f92;
        font-size: 11px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 6px 12px !important;
        font-size: 11px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #0b2340 !important;
        border-color: #0b2340 !important;
    }

    @media (max-width: 767px) {
        .icost-page { padding: 0 0 25px 0; }
        .icost-page-header { padding: 16px; }
        .icost-page-title { font-size: 25px; }
        .icost-page-date { margin-top: 12px; }
    }

    /* Reply indicators use the row's server-provided new_reply value. */
    .rfq-reference { display: flex; flex-direction: column; align-items: flex-start; gap: 7px; }
    .rfq-reply-status { display: inline-flex; align-items: center; gap: 6px; font-size: 11px; line-height: 1.4; white-space: normal; }
    .rfq-reply-status.is-new { padding: 4px 8px; border-radius: 6px; background: #fff1f2; color: #a61936; font-weight: 700; }
    .rfq-reply-status.is-read { color: #60758a; }
    .rfq-reply-status.is-unknown { color: #7b8794; }
    #quotations-datatable tbody tr.rfq-has-new-reply td { background: #fff8f1; }
    #quotations-datatable tbody tr.rfq-has-new-reply:hover td { background: #fff1e4; }
    #quotations-datatable tbody tr.rfq-has-new-reply td:first-child { box-shadow: inset 4px 0 #d64b42; }
    .rfq-reply-notice { margin: 16px 16px 0; padding: 12px 14px; border: 1px solid #d4e2ec; border-radius: 9px; background: #f4f8fb; color: #35506a; font-size: 12px; line-height: 1.6; }
    .rfq-reply-notice.is-warning { background: #fff8ed; border-color: #efdab3; color: #795719; }
    .rfq-reply-intro { margin: 4px 0 8px; }
    .rfq-reply-links { margin: 0; padding-left: 18px; }
    .rfq-reply-links li + li { margin-top: 5px; }
    .rfq-reply-links a { color: #0d65a5; font-weight: 700; text-decoration: underline; overflow-wrap: anywhere; }
    .rfq-reply-notice[hidden] { display: none !important; }
</style>


<section class="content">

    @include('layouts.flash.alert')


    <div class="container-fluid icost-page">

        <!-- Page Header -->
        <div class="icost-page-header">

            <div>
                <nav class="page-crumbs" aria-label="Breadcrumb">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <span aria-hidden="true">/</span>
                    <span>Requests for Quotation</span>
                </nav>

                <h1 class="icost-page-title">
                    Requests for Quotation (RFQs)
                </h1>

                <p class="icost-page-subtitle">
                    Create, manage and send supplier quotation requests
                </p>
            </div>

            <div class="icost-page-date">
                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                {{ now()->format('D, d M Y') }}
            </div>

        </div>


        <!-- Summary Cards -->
        <div class="row quotation-summary-row">

            <!-- Total RFQs -->
            <div class="col-lg-4 col-md-6">

                <div class="quotation-summary-card">

                    <div class="quotation-summary-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>

                    <div class="quotation-summary-content">

                        <div class="quotation-summary-label">
                            Total RFQs
                        </div>

                        <div
                            class="quotation-summary-value"
                            id="totalQuotationCount">
                            0
                        </div>

                        <div class="quotation-summary-description">
                            Matching RFQs
                        </div>

                    </div>

                </div>

            </div>


            <!-- Current Project -->
            <div class="col-lg-4 col-md-6">

                <div class="quotation-summary-card">

                    <div class="quotation-summary-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>

                    <div class="quotation-summary-content">

                        <div class="quotation-summary-label">
                            Current Project
                        </div>

                        <div
                            class="quotation-summary-value project-value"
                            id="currentProjectName">
                            Selected project
                        </div>

                        <div class="quotation-summary-description">
                            Selected project
                        </div>

                    </div>

                </div>

            </div>


            <!-- Next Delivery -->
            <div class="col-lg-4 col-md-12">

                <div class="quotation-summary-card">

                    <div class="quotation-summary-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>

                    <div class="quotation-summary-content">

                        <div class="quotation-summary-label">
                            Next Delivery
                        </div>

                        <div
                            class="quotation-summary-value"
                            id="nextDeliveryDate">
                            No date
                        </div>

                        <div class="quotation-summary-description">
                            Upcoming delivery
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Main Quotations Card -->
        <div class="quotation-main-card">

            <!-- Card Header -->
            <div class="quotation-card-header">

                <div class="quotation-title-wrapper">

                    <div class="quotation-card-icon">
                        <i class="fas fa-database"></i>
                    </div>

                    <div>

                        <h2 class="quotation-card-title">
                            Requests for Quotation
                        </h2>

                        <div class="quotation-card-subtitle">
                            Manage supplier quotation requests
                        </div>

                    </div>

                </div>


                @if (auth()->user()->can('access', 'purchase orders add'))

                    <button
                        type="button"
                        onclick="window.location.href='{{ route('quotations.create') }}'"
                        class="quotation-create-button">

                        <i class="fas fa-plus"></i>

                        Create RFQ

                    </button>

                @endif

            </div>


            <!-- Search And Project Filter -->
            <div class="quotation-toolbar">

                <div class="row align-items-end">

                    <div class="col-lg-7 col-md-7">

                        <label class="quotation-project-label" for="quotationSearch">
                            Search
                        </label>

                        <div class="quotation-search-wrapper">

                            <i class="fas fa-search quotation-search-icon"></i>

                            <input
                                id="quotationSearch"
                                class="quotation-search-input"
                                type="text"
                                autocomplete="off"
                                placeholder="Search RFQs...">

                        </div>

                    </div>


                    <div class="col-lg-5 col-md-5 quotation-project-filter">

                        <label
                            class="quotation-project-label"
                            for="project_filter">
                            Project
                        </label>

                        {{ Form::select(
                            'project_filter_id',
                            $projects,
                            $selectedProject ?? auth()->user()->default_project,
                            [
                                'class' => 'form-control multiselect-dropdown',
                                'id' => 'project_filter',
                                'data-live-search' => 'true'
                            ]
                        ) }}

                    </div>

                </div>

            </div>


            <div id="rfqReplyNotice" class="rfq-reply-notice" role="status" aria-live="polite" hidden></div>

            <!-- RFQs Table -->
            <div class="quotation-table-area">

                <table
                    class="table"
                    id="quotations-datatable"
                    data-table="quotations">

                    <thead>

                        <tr>

                            <th>
                                Originator
                            </th>

                            <th>
                                RFQ No
                            </th>

                            <th>
                                Notes
                            </th>

                            <th>
                                Delivery Date
                            </th>

                            <th>
                                Delivery Address
                            </th>

                            <th class="project-actions no-sort">
                                Actions
                            </th>

                        </tr>

                    </thead>

                </table>

            </div>

        </div>

    </div>

</section>

@stop


@push('scripts')

<script type="text/javascript">

$(document).ready(function () {

    function escapeText(value) {
        return $('<span>').text(value == null ? '' : String(value)).html();
    }

    // Accept explicit booleans and common serialized equivalents only.
    // Missing or unrecognized values are unknown, not "no unread replies".
    function replyState(row) {
        if (!row || !Object.prototype.hasOwnProperty.call(row, 'new_reply')) {
            return null;
        }
        var value = row.new_reply;
        if (typeof value === 'string') {
            value = value.trim().toLowerCase();
        }
        if (value === true || value === 1 || value === '1' || value === 'true') {
            return true;
        }
        if (value === false || value === 0 || value === '0' || value === 'false') {
            return false;
        }
        return null;
    }

    function updateReplyNotice(rows, json) {
        var notice = $('#rfqReplyNotice');
        var summary = json && Array.isArray(json.unread_rfqs) ? json.unread_rfqs : null;

        if (summary !== null) {
            notice.empty().removeClass('is-warning');
            if (!summary.length) {
                notice.prop('hidden', true);
                return;
            }

            $('<strong>').text(
                'New supplier replies: ' + summary.length +
                (summary.length === 1 ? ' RFQ' : ' RFQs')
            ).appendTo(notice);

            $('<p>').addClass('rfq-reply-intro')
                .text('Across your accessible projects. Open an RFQ below to read its reply, including requests outside the current table filters.')
                .appendTo(notice);

            var list = $('<ul>').addClass('rfq-reply-links').appendTo(notice);
            summary.forEach(function (item) {
                var label = String(item.rfq || 'RFQ');
                if (item.project_name) {
                    label += ' — ' + String(item.project_name);
                }
                var entry = $('<li>').appendTo(list);
                var href = String(item.view_url || '');
                if (/^(https?:\/\/|\/(?!\/))/i.test(href)) {
                    $('<a>').attr('href', href).text(label).appendTo(entry);
                } else {
                    $('<span>').text(label).appendTo(entry);
                }
            });
            notice.prop('hidden', false);
            return;
        }

        // Compatible fallback while the previous endpoint is still deployed.
        var missingStatus = rows.some(function (row) { return replyState(row) === null; });
        var references = rows.filter(function (row) {
            return replyState(row) === true;
        }).map(function (row) {
            return String(row.rfq == null ? '' : row.rfq).replace(/<[^>]*>/g, '').trim();
        }).filter(function (reference) { return reference !== ''; });

        var message = references.length
            ? 'New supplier replies on this page: ' + references.join(', ') +
              '. Open the highlighted RFQ using its view button to read the reply.'
            : '';

        if (missingStatus) {
            message += (message ? ' ' : '') +
                'Reply updates are currently unavailable for some RFQs. Please refresh this page or contact support.';
        }

        notice.toggleClass('is-warning', missingStatus)
            .text(message)
            .prop('hidden', message === '');
    }


    /*
    |--------------------------------------------------------------------------
    | Update Current Project Card
    |--------------------------------------------------------------------------
    */

    function updateCurrentProject() {

        var projectName =
            $('#project_filter option:selected').text();

        if (
            projectName &&
            projectName.trim() !== ''
        ) {
            $('#currentProjectName').text(projectName);
        } else {
            $('#currentProjectName').text('All projects');
        }

    }


    /*
    |--------------------------------------------------------------------------
    | Format Delivery Date
    |--------------------------------------------------------------------------
    */

    function formatDisplayDate(dateValue) {

        if (!dateValue) {
            return 'No date';
        }

        var cleanDate =
            String(dateValue)
                .replace(/<[^>]*>/g, '')
                .trim();

        var parsedDate =
            new Date(cleanDate + 'T00:00:00');

        if (isNaN(parsedDate.getTime())) {
            return cleanDate;
        }

        return parsedDate.toLocaleDateString(
            'en-GB',
            {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Find Next Delivery From Loaded Results
    |--------------------------------------------------------------------------
    */

    function updateNextDelivery(rows) {

        if (!rows || rows.length === 0) {

            $('#nextDeliveryDate').text('No date');

            return;
        }


        var today = new Date();

        today.setHours(0, 0, 0, 0);


        var futureDates = [];


        rows.forEach(function (row) {

            if (!row.delivery_date) {
                return;
            }


            var dateText =
                String(row.delivery_date)
                    .replace(/<[^>]*>/g, '')
                    .trim();


            var parsedDate =
                new Date(dateText + 'T00:00:00');


            if (
                !isNaN(parsedDate.getTime()) &&
                parsedDate >= today
            ) {

                futureDates.push({

                    original: dateText,

                    date: parsedDate

                });

            }

        });


        futureDates.sort(function (a, b) {

            return a.date - b.date;

        });


        if (futureDates.length > 0) {

            $('#nextDeliveryDate').text(
                formatDisplayDate(
                    futureDates[0].original
                )
            );

        } else {

            $('#nextDeliveryDate').text('No upcoming date');

        }

    }


    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    var quotationTable =
        $('#quotations-datatable').DataTable({

            processing: true,

            serverSide: true,

            searching: true,

            responsive: false,

            autoWidth: false,

            pageLength: 10,

            lengthChange: false,


            /*
            |--------------------------------------------------------------------------
            | Hide DataTables Default Search
            | Keep Table, Information And Pagination
            |--------------------------------------------------------------------------
            */

            dom:
                'rt' +
                '<"row align-items-center mt-2"' +
                    '<"col-md-6"i>' +
                    '<"col-md-6"p>' +
                '>',


            ajax: {

                url: "{{ route('ajax.quotations.list.all') }}",

                type: 'GET',

                data: function (d) {

                    d.project_filter_id =
                        $('#project_filter').val();

                },

                dataSrc: function (json) {

                    /*
                    |--------------------------------------------------------------------------
                    | Total Quotation Count
                    |--------------------------------------------------------------------------
                    */

                    var total =
                        json.recordsFiltered !== undefined
                            ? json.recordsFiltered
                            : (
                                json.recordsTotal !== undefined
                                    ? json.recordsTotal
                                    : 0
                            );


                    $('#totalQuotationCount').text(total);


                    /*
                    |--------------------------------------------------------------------------
                    | Next Delivery
                    |--------------------------------------------------------------------------
                    */

                    updateNextDelivery(json.data || []);
                    updateReplyNotice(json.data || [], json);


                    return json.data || [];

                }

            },


            columns: [

                {
                    data: 'name',
                    name: 'name'
                },

                {
                    data: 'rfq',
                    name: 'rfq',
                    render: function (
                        data,
                        type,
                        row
                    ) {

                        if (type !== 'display') {
                            return data;
                        }

                        var state = replyState(row);
                        var status = state === true
                            ? '<span class="rfq-reply-status is-new"><i class="fas fa-envelope" aria-hidden="true"></i> New supplier reply</span>'
                            : (state === false
                                ? '<span class="rfq-reply-status is-read">No new replies</span>'
                                : '<span class="rfq-reply-status is-unknown">Reply status unavailable</span>');

                        return '<div class="rfq-reference">' +
                            '<span class="rfq-number-badge">' + escapeText(data) + '</span>' +
                            status + '</div>';

                    }
                },

                {
                    data: 'notes',
                    name: 'notes',
                    defaultContent: ''
                },

                {
                    data: 'delivery_date',
                    name: 'delivery_date'
                },

                {
                    data: 'delivery_address',
                    name: 'delivery_address',
                    defaultContent: ''
                },

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }

            ],


            rowCallback: function (row, data) {
                var isNew = replyState(data) === true;
                $(row).toggleClass('rfq-has-new-reply', isNew);
                if (isNew) {
                    $(row).find('td:last-child a').filter(function () {
                        return $(this).find('.fa-eye').length > 0;
                    }).attr({
                        title: 'Open RFQ and read the new supplier reply',
                        'aria-label': 'Open RFQ ' + String(data.rfq || '') + ' and read the new supplier reply'
                    });
                }
            },

            columnDefs: [

                {
                    targets: 'no-sort',
                    orderable: false
                }

            ],


            language: {

                processing:
                    'Loading RFQs...',

                emptyTable:
                    'No RFQs found',

                zeroRecords:
                    'No matching RFQs found',

                info:
                    'Showing _START_ to _END_ of _TOTAL_ entries',

                infoEmpty:
                    'Showing 0 entries',

                paginate: {

                    previous:
                        'Previous',

                    next:
                        'Next'

                }

            }

        });


    /*
    |--------------------------------------------------------------------------
    | Initial Project Name
    |--------------------------------------------------------------------------
    */

    updateCurrentProject();


    /*
    |--------------------------------------------------------------------------
    | Project Filter
    |--------------------------------------------------------------------------
    */

    $('#project_filter').on(
        'change',
        function () {

            updateCurrentProject();

            quotationTable
                .page('first')
                .draw(false);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    var searchTimer;


    $('#quotationSearch').on(
        'keyup input',
        function () {

            clearTimeout(searchTimer);


            var searchValue =
                this.value;


            searchTimer =
                setTimeout(
                    function () {

                        quotationTable
                            .search(searchValue)
                            .draw();

                    },
                    300
                );

        }
    );

});

</script>

@endpush
