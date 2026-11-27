# Translating the i-cost.co.uk pages (Spanish, French, German)

Built with `icost-tools/website-tools/i18n.py`. English is the source. Each page's text is split into segments:
`i18n/source/<page>.json` = `{id: {"en": English}}`; what a language still needs is in `i18n/todo/<lang>/<page>.json`
= `{id: English}`. Translations go in `i18n/<lang>/<page>.json` = `{id: "translation"}` (same ids). Check a file with
`python icost-tools/website-tools/i18n_check.py <lang> <page>`, then build with `python i18n.py build`.

## What I-Cost is
I-Cost is software for UK construction companies (main contractors, subcontractors, fit-out firms): estimating and
pricing a bill of quantities, client quotations, supplier price requests, purchase orders, deliveries, site hours,
the programme, embodied carbon and cost reports, in one system. Readers: company directors and owners, estimators and
quantity surveyors, buyers, project and site managers, finance. The site is marketing: clear, plain, confident, no hype.

## Voice
- Spanish: Spain Spanish, formal **usted** throughout. French: France French, **vous**. German: Germany German, **Sie**.
- Plain and short, like the English. Buttons, menu items, chips and tags must stay short (German especially).
- Headings stay headings (sentence case, as in the English). ALL CAPS text stays ALL CAPS.

## Keep exactly as written
- Names: I-Cost, I-COST, Xero, Autodesk, Revit, I-Cost Model Link, MS Project / Microsoft Project, Excel, Word, PDF, CSV,
  IFC, BIM, QR, API, AI, Claude, Google, Cyber Essentials, ISO/IEC 27001, RICS, NRM, NRM2, BS EN 15978, ÖKOBAUDAT,
  Companies House, people, companies, projects and places (Kingsway House, Northfield Drylining, City Office Fit-Out,
  Hatfield...), document numbers (PO-1043, DN-2231, RFQ...).
- Numbers, money (£), percentages, dates and times exactly as in the English (do not change decimal points or commas,
  do not convert £).
- Facts: £35 per user per month; 7-day free trial; no bank card required; "aligning with ISO 27001, not certified"
  must keep its meaning (NOT certified); "UK" stays (the product is made for UK contractors and UK rules: do not swap in
  Spanish, French or German regulations). The I-Cost app's screens can be shown in Spanish, French, German or Polish by the browser (Chrome or Edge, My Account > Language); emails and reports are in English. Do not promise more than that.

## Placeholders and markup
- `<1>…</1>` and `<2/>` stand for formatting, links and icons. Keep **every** placeholder of the segment, the same
  numbers, each once; you may move them to fit the word order. Translate the text inside them. Add no other tags.
- Entities such as `&amp;`, `&middot;`, `&pound;`, `&rarr;`, `&#8599;` may stay as they are or be written as the
  character; write a plain ampersand as `&amp;`.
- A segment that is only names, codes or numbers: return it unchanged.

## Construction words
| English | Spanish | French | German |
|---|---|---|---|
| bill of quantities (BOQ) | mediciones (BOQ) | DQE (BOQ) | Leistungsverzeichnis (LV) |
| estimate / estimating | presupuesto / presupuestación | chiffrage / estimation | Kalkulation |
| quantity surveyor / estimator | técnico de costes / presupuestador | économiste de la construction | Kalkulator |
| request for quotation (RFQ) | solicitud de oferta (RFQ) | demande de prix (RFQ) | Preisanfrage |
| supplier quote / prices | oferta / precios del proveedor | offre / prix du fournisseur | Angebot / Lieferantenpreise |
| purchase order (PO) | pedido de compra | bon de commande | Bestellung |
| supplier | proveedor | fournisseur | Lieferant |
| main contractor | contratista principal | entreprise générale | Generalunternehmer |
| subcontractor | subcontratista | sous-traitant | Nachunternehmer |
| delivery note | albarán | bon de livraison | Lieferschein |
| timesheet / site hours | parte de horas / horas en obra | feuille de temps / heures sur chantier | Stundenzettel / Baustellenstunden |
| programme (schedule) | planificación de obra | planning | Bauzeitenplan |
| embodied carbon | carbono incorporado | carbone intrinsèque | graue Emissionen |
| whole life carbon | carbono del ciclo de vida completo | carbone sur le cycle de vie | Lebenszyklus-Emissionen |
| margin | margen | marge | Marge |
| committed cost | coste comprometido | coût engagé | gebundene Kosten |
| forecast final cost | coste final previsto | coût final prévisionnel | prognostizierte Endkosten |
| variation (extra work) | modificado | travaux supplémentaires (avenant) | Nachtrag |
| cost report | informe de costes | rapport de coûts | Kostenbericht |
| site manager | jefe de obra | conducteur de travaux | Bauleiter |
| buyer | comprador | acheteur | Einkäufer |
| director / owner | director / propietario | dirigeant | Geschäftsführer / Inhaber |
| free trial | prueba gratuita | essai gratuit | kostenlose Testphase |
| item list / buy list | lista de partidas / lista de compra | liste des articles / liste d'achats | Positionsliste / Einkaufsliste |
