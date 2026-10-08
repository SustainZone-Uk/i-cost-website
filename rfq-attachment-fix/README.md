# RFQ reply visibility update

These Laravel files are application deployment files. This repository contains the marketing website; publishing the website alone does not install the RFQ changes in the iCost application.

Deploy the following files to the matching paths in the application:

- `Modules/PurchaseManager/Http/Controllers/CommanAjaxController.php`
- `Modules/PurchaseManager/Resources/views/quotations/index.blade.php`

The existing `QuotationReplyReceived` notification class must provide `unreadQuotationIds()`. The existing `QuotationManagerController::show()` marks a request's notifications read using `QuotationReplyReceived::markRead()`.

The RFQ list now identifies unread replies beside each reference, highlights affected rows, and includes a summary with links across the current user's accessible projects. The summary is independent of the table's project filter, search and pagination. The heading is Requests for Quotation (RFQs).

Validation performed: PHP syntax check and JavaScript checks for reply flags, unread links outside the displayed table results, rejected unsafe link protocols and empty unread state. Live Laravel/database testing is still required after deployment.
