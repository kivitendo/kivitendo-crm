
const billaddrFormModel = [
        { "name": "billaddr-greetings", "label": "Greetings:", "type": "select", "data":[], "tooltip":""}, //selectbox
        { "name": "billaddr-greeting", "label": "Greetings:", "type": "input", "size": "42", "tooltip": "Alternativ greetings" },
        { "name": "billaddr-name", "label": "Name:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
        { "name": "billaddr-street", "label": "Street:", "type": "input", "size": "42", "tooltip": "Street and street number" },
        { "name": "billaddr-country", "label": "Country code:", "type": "input", "size": "4", "tooltip": "Country code", "data": "D" },
        { "name": "billaddr-zipcode", "label": "Zip code:", "type": "input", "size": "12", "tooltip": "Zip code" },
        { "name": "billaddr-city", "label": "City:", "type": "input", "size": "42", "tooltip": "" },
        { "name": "billaddr-bland", "label": "Bundesland:", "type": "select", "data":[], "tooltip":""}, //selectbox
        { "name": "billaddr-phone", "label": "Phone:", "type": "input", "size": "42", "tooltip": "First phone number" },
        { "name": "billaddr-fax", "label": "Phone:", "type": "input", "size": "42", "tooltip": "Second phone number" },
        { "name": "billaddr-email", "label": "E-Mail:", "type": "input", "size": "42", "tooltip": "" },
        { "name": "billaddr-contact", "label": "Contact person:", "type": "input", "size": "42", "tooltip": "Contact person" },
        { "name": "billaddr-business_id", "label": "Costumer type:", "type": "select","data": [], "tooltip":""}, //selectbox
        { "name": "billaddr-sw", "label": "Keyword:", "type": "input", "size": "42", "tooltip": "Keyword" },
        { "name": "billaddr-notes", "label": "Comment:", "type": "textarea", "cols": "42", "rows": "5", "tooltip": "Comment" },
        { "name": "billaddr-currency_id", "type": "hidden", "data": "1"},
        { "name": "billaddr-id", "type": "hidden", },
        { "name": "billaddr-src", "type": "hidden", },
    ];

const deladdrFormModel = [
    { "name": "deladdr-list", "label": "Deliver address:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "deladdr-shiptoname", "label": "Name:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptodepartment_1", "label": "Department 1:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptodepartment_2", "label": "Department 2:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptostreet", "label": "Street:", "type": "input", "size": "42", "tooltip": "Street and street number" },
    { "name": "deladdr-shiptocountry", "label": "Country code:", "type": "input", "size": "2", "tooltip": "Country code", "data": "D" },
    { "name": "deladdr-shiptozipcode", "label": "Zip code:", "type": "input", "size": "12", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptobland", "label": "Bundesland:", "type": "select", "data":[], "tooltip":""}, //selectbox
    { "name": "deladdr-shiptocity", "label": "City:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptophone", "label": "Phone:", "type": "input", "size": "42", "tooltip": "First phone number" },
    { "name": "deladdr-shiptofax", "label": "Phone:", "type": "input", "size": "42", "tooltip": "Second phone number" },
    { "name": "deladdr-shiptoemail", "label": "E-Mail:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "deladdr-shiptocontact", "label": "Contact person:", "type": "input", "size": "42", "tooltip": "Contact person" },
];

const banktaxFormModel = [
    { "name": "billaddr-ustid", "label": "UStId:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-taxnumber", "label": "Tax number:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-bank", "label": "Bank name:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-bank_code", "label": "BLZ:", "type": "input", "size": "42", "tooltip": "Country code" },
    { "name": "billaddr-account_number", "label": "Account:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-iban", "label": "IBAN:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-bic", "label": "BIC:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-direct_debit", "label": "Lastschrift:", "type": "checkbox", "size": "42", "tooltip": "" },
];

const extraFormModel = [
    { "name": "billaddr-branches", "label": "Industry:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "billaddr-branche", "label": "Industry:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-homepage", "label": "Homepage:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-department_1", "label": "Department:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-department_2", "label": "Department:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-lead", "label": "Lead:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "billaddr-leadsrc", "label": "Lead source:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-taxzone_id", "label": "Tax zone:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "billaddr-payment_id", "label": "Terms of payment:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "billaddr-headcount", "label": "Amount of employees:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-konzern", "label": "Busines group:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-language", "label": "Language:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "billaddr-salesman_id", "label": "Salesperson:", "type": "select","data": [], "tooltip":""}, //selectbox
];

const carFormModel = [
    { "name": "car-c_ln", "label": "Car license:", "type": "input", "size": "21", "tooltip": "", "check": "chk_c_ln", "info": "c_ln_info" },
    { "name": "car-c_2", "label": "HSN (2.1):", "type": "input", "size": "21", "tooltip": "", "check": "chk_c_2" },
    { "name": "car-c_3", "label": "TSN (2.2):", "type": "input", "size": "21", "tooltip": "", "check": "chk_c_3" },
    { "name": "car-c_em", "label": "Emission class (14.1):", "type": "input", "size": "21", "tooltip": "", "check": "chk_c_em", "info": "c_em_info" },
    { "name": "car-c_d", "label": "Date of registration:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_hu", "label": "Date of HU+AU:", "type": "input", "size": "21", "tooltip": "", "check": "chk_c_hu" },
    { "name": "car-c_fin", "label": "FIN:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_finchk", "label": "FIN check:", "type": "input", "size": "2", "tooltip": "" },
    { "name": "car-c_mkb", "label": "Engine code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_color", "label": "Color code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_gart_list", "label": "Engine code:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "car-c_gart", "label": "Color code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_st", "label": "Summer wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_wt", "label": "Winter wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_st_l", "label": "LO Sommer wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_st_w", "label": "LO Winter wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-flxgr", "label": "Flexrohr size:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_zrd", "label": "Next ZR change on:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_zrk", "label": "Next ZR change at KM:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_bf", "label": "Next brake fluid change:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_wd", "label": "Next maintenance service:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-1", "label": "Manufacture:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-2", "label": "Typ:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-3", "label": "Displacement:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-4", "label": "Construction year:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-5", "label": "Performance:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-6", "label": "Torque:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-7", "label": "Compression:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-8", "label": "Valves:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-9", "label": "Cylinder:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-10", "label": "Fuel Type / Content:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-11", "label": "Wheelbase:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-12", "label": "Vmax:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "kba-13", "label": "Total weight:", "type": "input", "size": "21", "tooltip": "" },
];

const editCarFormModel = [
    { "name": "edit_car-c_ln", "label": "Car license:", "type": "input", "size": "21", "tooltip": "", "check": "edit_car-chk_c_ln", "info": "edit_car-c_ln_info" },
    { "name": "edit_car-c_2", "label": "HSN (2.1):", "type": "input", "size": "21", "tooltip": "", "check": "edit_car-chk_c_2" },
    { "name": "edit_car-c_3", "label": "TSN (2.2):", "type": "input", "size": "21", "tooltip": "", "check": "edit_car-chk_c_3" },
    { "name": "edit_car-c_em", "label": "Emission class (14.1):", "type": "input", "size": "21", "tooltip": "", "check": "edit_car-chk_c_em", "info": "edit_car-c_em_info" },
    { "name": "edit_car-c_d", "label": "Date of registration:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_hu", "label": "Date of HU+AU:", "type": "input", "size": "21", "tooltip": "", "check": "edit_car-chk_c_hu" },
    { "name": "edit_car-c_fin", "label": "FIN:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_finchk", "label": "FIN check:", "type": "input", "size": "2", "tooltip": "" },
    { "name": "edit_car-c_mkb", "label": "Engine code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_color", "label": "Color code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_gart_list", "label": "Engine code:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "edit_car-c_gart", "label": "Color code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_st", "label": "Summer wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_wt", "label": "Winter wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_st_l", "label": "LO Sommer wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_st_w", "label": "LO Winter wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-flxgr", "label": "Flexrohr size:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_zrd", "label": "Next ZR change on:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_zrk", "label": "Next ZR change at KM:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_bf", "label": "Next brake fluid change:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_wd", "label": "Next maintenance service:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-1", "label": "Manufacture:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-2", "label": "Typ:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-3", "label": "Displacement:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-4", "label": "Construction year:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-5", "label": "Performance:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-6", "label": "Torque:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-7", "label": "Compression:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-8", "label": "Valves:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-9", "label": "Cylinder:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-10", "label": "Fuel Type / Content:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-11", "label": "Wheelbase:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-12", "label": "Vmax:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car_kba-13", "label": "Total weight:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_id", "type": "hidden", },
];

const editArticleFormModel = [
    { "name": "edit_article-partnumber", "label": "Article number:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_article-description", "label": "Description:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_article-part_type", "label": "Article typ:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "edit_article-unit", "label": "Unit:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "edit_article-qty", "label": "Quantity:", "type": "input", "size": "15", "tooltip": "" },
    { "name": "edit_article-listprice", "label": "Purchasing price:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_article-sellprice", "label": "Sales price:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_article-buchungsgruppen_id", "label": "Booking group:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "edit_article-parts_id", "type": "hidden", },
];

// Namensschema für id, name und class: Das Minuszeichen ist das Trennzeichen zwischen Präfix (z.B. edit_article) und Spaltenname in der Datenbank (z.B. parts_id)
// hidden Inputfelder immer an letzter Position in der Vorlagenliste (z.B. editArticleFormModel)

const searchOrderFormModel = [
    { "name": "search_order-ln", "label": "license number:", "type": "input", "size": "21", "tooltip": "license number" },
    { "name": "search_order-name", "label": "name:", "type": "input", "size": "21", "tooltip": "name" },
    { "name": "search_order-from", "label": "from:", "type": "input", "size": "21", "tooltip": "from" },
    { "name": "search_order-to", "label": "to:", "type": "input", "size": "21", "tooltip": "to" },
    { "name": "search_order-status", "label": "status:", "type": "select","data": ['','angenommen','bearbeitet','abgerechnet'], "tooltip":"status"}, //selectbox
    { "name": "search_order-car_license", "label": "Car license:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "search_order-customer_name", "label": "Customer name:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "search_order-car_manuf", "label": "Car manufacturer:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "search_order-car_type", "label": "Car type:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "search_order-car_brand", "label": "Car brand:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "search_order-status", "label": "Status:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "search_order-date_from", "label": "Date from:", "type": "input", "size": "16", "tooltip": "" },
    { "name": "search_order-date_to", "label": "Date to:", "type": "input", "size": "16", "tooltip": "" },
 ];
