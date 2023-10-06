const billaddrFormModel = [
    { "name": "billaddr-name", "label": "Name:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "billaddr-greetings", "label": "Greetings:", "type": "select", "data":[], "tooltip":""}, //selectbox
    { "name": "billaddr-greeting", "label": "Greetings:", "type": "input", "size": "42", "tooltip": "Alternativ greetings" },
    { "name": "billaddr-street", "label": "Street:", "type": "input", "size": "42", "tooltip": "Street and street number" },
    { "name": "billaddr-country", "label": "Country code:", "type": "input", "size": "4", "tooltip": "Country code", "data": "D" },
    { "name": "billaddr-zipcode", "label": "Zip code:", "type": "input", "size": "12", "tooltip": "Zip code" },
    { "name": "billaddr-city", "label": "City:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-bland", "label": "Bundesland:", "type": "select", "data":[], "tooltip":""}, //selectbox
    { "name": "billaddr-phone", "label": "Phone1:", "type": "input", "size": "42", "tooltip": "First phone number" },
    { "name": "billaddr-note_phone", "label": "Phone1 note:", "type": "input", "size": "42", "tooltip": "First phone number" },
    { "name": "billaddr-fax", "label": "Phone2:", "type": "input", "size": "42", "tooltip": "Second phone number" },
    { "name": "billaddr-note_fax", "label": "Phone2 note:", "type": "input", "size": "42", "tooltip": "Second phone number" },
    { "name": "billaddr-phone3", "label": "Phone3:", "type": "input", "size": "42", "tooltip": "Second phone number" },
    { "name": "billaddr-note_phone3", "label": "Phone3 note:", "type": "input", "size": "42", "tooltip": "Second phone number" },
    { "name": "billaddr-email", "label": "E-Mail:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-contact", "label": "Contact person:", "type": "input", "size": "42", "tooltip": "Contact person" },
    { "name": "billaddr-business_id", "label": "Costumer type:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "billaddr-sw", "label": "Keyword:", "type": "input", "size": "42", "tooltip": "Keyword" },
    { "name": "billaddr-notes", "label": "Comment:", "type": "textarea", "cols": "42", "rows": "5", "tooltip": "Comment" },
    { "name": "billaddr-currency_id", "type": "hidden", "data": "1"}, //ToDo darf nicht statisch sein. Beim Praktikaten abgucken. selbiges gilt für currency.
    { "name": "billaddr-id", "type": "hidden", },
    { "name": "billaddr-src", "type": "hidden", },
];

const contactPersonFormModel = [
    { "name": "contacts-cp_givenname", "label": "Givenname:", "type": "input", "size": "42", "tooltip": "", "autofocus": "true" },
    { "name": "contacts-cp_title", "label": "Title:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_name", "label": "Name:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_abteilung", "label": "Department:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_homepage", "label": "Homepage:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_street", "label": "Street:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_country", "label": "Country code:", "type": "input", "size": "2", "tooltip": "", "data": "D" },
    { "name": "contacts-cp_zipcode", "label": "Zip code:", "type": "input", "size": "12", "tooltip": "" },
    { "name": "contacts-cp_city", "label": "City:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_phone1", "label": "Phone 1:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_phone2", "label": "Phone 2:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_mobile1", "label": "Mobile 1:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_mobile2", "label": "Mobile 2:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_privatphone", "label": "Privat phone:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_email", "label": "E-mail:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_privatemail", "label": "Private e-mail:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts-cp_stichwort1", "label": "Keyword:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts_company_name", "label": "Firm:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "contacts_cp_id", "type": "hidden", },
    { "name": "contacts-cp_cv_id", "type": "hidden", },
    { "name": "contacts_src", "type": "hidden", },
];

const deladdrFormModel = [
    { "name": "deladdr-list", "label": "Deliver address:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "deladdr-shiptoname", "label": "Name:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptodepartment_1", "label": "Department 1:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptodepartment_2", "label": "Department 2:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptostreet", "label": "Street:", "type": "input", "size": "42", "tooltip": "Street and street number" },
    { "name": "deladdr-shiptocountry", "label": "Country code:", "type": "input", "size": "2", "tooltip": "Country code", "data": "D" },
    { "name": "deladdr-shiptozipcode", "label": "Zip code:", "type": "input", "size": "12", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptocity", "label": "City:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptobland", "label": "Bundesland:", "type": "select", "data":[], "tooltip":""}, //selectbox
    { "name": "deladdr-shiptophone", "label": "Phone:", "type": "input", "size": "42", "tooltip": "First phone number" },
    { "name": "deladdr-shiptofax", "label": "fax:", "type": "input", "size": "42", "tooltip": "Second phone number" },
    { "name": "deladdr-shiptoemail", "label": "E-Mail:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "deladdr-shiptocontact", "label": "Contact person:", "type": "input", "size": "42", "tooltip": "Contact person" },
    { "name": "deladdr_shipto_id", "type": "hidden", },
]


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
    { "name": "car-c_hu", "label": "Date of HU+AU:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_fin", "label": "FIN:", "type": "input", "size": "21", "tooltip": "", "check": "chk_c_fin"},
    { "name": "car-c_finchk", "label": "FIN check:", "type": "input", "size": "1", "tooltip": "" },
    { "name": "car-c_mkb", "label": "Engine code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_color", "label": "Color code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_gart_list", "label": "Engine code:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "car-c_gart", "label": "Color code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_st", "label": "Summer wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_wt", "label": "Winter wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_st_l", "label": "LO Sommer wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_wt_l", "label": "LO Winter wheels:", "type": "input", "size": "21", "tooltip": "" },
    //{ "name": "car-flxgr", "label": "Flexrohr size:", "type": "input", "size": "21", "tooltip": "" }, // siehe DB-Tabelle lxc_flex (wird nicht genutzt)
    { "name": "car-c_zrd", "label": "Next ZR change on:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_zrk", "label": "Next ZR change at KM:", "type": "number", "size": "21", "tooltip": "" },
    { "name": "car-c_bf", "label": "Next brake fluid change:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_wd", "label": "Next maintenance service:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "car-c_text", "label": "Comment:", "type": "textarea", "cols": "27", "rows": "5", "tooltip": "Comment" },
    { "name": "car-kba_id", "type": "hidden", },
];

const carKbaFormModel = [
    { "name": "car_kba-hersteller", "label": "Manufacture:", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-fhzart", "label": "Kind:", "type": "select","data": { '': '', 'car': kivi.t8( 'car' ), 'truck': kivi.t8( 'truck' ), 'trailer': kivi.t8( 'trailer' ), 'bike': kivi.t8( 'bike' ), 'tractor': kivi.t8( 'tracktor' ) }, "tooltip":"", "disabled": "true" }, //selectbox
    { "name": "car_kba-d2", "label": "Type code (d2):", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "car_kba-name", "label": "Type name:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "car_kba-hubraum", "label": "Displacement:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "car_kba-leistung", "label": "Performance:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "car_kba-kraftstoff", "label": "Fuel Type / Content:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "car_kba-achsen", "label": "Wheelbase:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "car_kba-masse", "label": "Total weight:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "car_kba-t", "label": "Vmax:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "car_kba-field_14_1", "label": "Emission class:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "car_kba-field_7_1", "label": "Axle load (field_7_1):", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "car_kba-field_7_2", "label": "Axle load (field_7_2):", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "car_kba_edit", "label": "Edit KBA*", "type": "button", "tooltip": "" },
    { "name": "car_kba_hide_show", "label": "Show extra fields", "type": "button", "tooltip": "" },
    { "name": "car_kba-marke", "label": "Brand", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-klasse", "label": "Class", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-datum", "label": "Date", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-aufbau", "label": "Construction", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-antrieb", "label": "Drive", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-sitze", "label": "Seats", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-d3", "label": "d3", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-j", "label": "j", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_4", "label": "field_4", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-d1", "label": "d1", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_2", "label": "field_2", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_5", "label": "field_5", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-v9", "label": "v9", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_14", "label": "field_14", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-p3", "label": "p3", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_10", "label": "field_10", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_14_1", "label": "field_14_1", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-p1", "label": "p1", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-l", "label": "l", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_9", "label": "field_9", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-p2_p4", "label": "p2_p4", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-l", "label": "l", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_18", "label": "field_18", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_19", "label": "field_19", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_20", "label": "filed_20", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-g", "label": "g", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_12", "label": "field_12", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_13", "label": "field_13", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-q", "label": "q", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-v7", "label": "v7", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-f1", "label": "f1", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-f2", "label": "f2", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_7_3", "label": "field_7_3", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_8_1", "label": "field_8_1", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_8_2", "label": "field_8_2", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_8_3", "label": "field_8_3", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-u1", "label": "u1", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-u2", "label": "u2", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-u3", "label": "u3", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-o1", "label": "o1", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-o2", "label": "o2", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-s1", "label": "s1", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-s2", "label": "s2", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_15_1", "label": "field_15_1", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_15_2", "label": "field_15_2", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_15_3", "label": "filed_15_3", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-k", "label": "k", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_6", "label": "field_6", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_17", "label": "field_17", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "car_kba-field_21", "label": "field_21", "class": "car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
];

const editCarKbaFormModel = [
    { "name": "edit_car_kba-hersteller", "label": "Manufacture:", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-fhzart", "label": "Kind:", "type": "select","data": { '': '', 'car': kivi.t8( 'car' ), 'truck': kivi.t8( 'truck' ), 'trailer': kivi.t8( 'trailer' ), 'bike': kivi.t8( 'bike' ), 'tractor': kivi.t8( 'tracktor' ) }, "tooltip":"", "disabled": "true" }, //selectbox
    { "name": "edit_car_kba-d2", "label": "Type code (d2):", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "edit_car_kba-name", "label": "Type name:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "edit_car_kba-hubraum", "label": "Displacement:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "edit_car_kba-leistung", "label": "Performance:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "edit_car_kba-kraftstoff", "label": "Fuel Type / Content:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "edit_car_kba-achsen", "label": "Wheelbase:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "edit_car_kba-masse", "label": "Total weight:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "edit_car_kba-t", "label": "Vmax:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "edit_car_kba-field_14_1", "label": "Emission class:", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "edit_car_kba-field_7_1", "label": "Axle load (field_7_1):", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "edit_car_kba-field_7_2", "label": "Axle load (field_7_2):", "type": "input", "size": "21", "tooltip": "", "readonly": "true"  },
    { "name": "edit_car_kba_edit", "label": "Edit KBA*", "type": "button", "tooltip": "" },
    { "name": "edit_car_kba_hide_show", "label": "Show extra fields", "type": "button", "tooltip": "" },
    { "name": "edit_car_kba-marke", "label": "Brand", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-klasse", "label": "Class", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-datum", "label": "Date", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-aufbau", "label": "Construction", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-antrieb", "label": "Drive", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-sitze", "label": "Seats", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-d3", "label": "d3", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-j", "label": "j", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_4", "label": "field_4", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-d1", "label": "d1", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_2", "label": "field_2", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_5", "label": "field_5", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-v9", "label": "v9", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_14", "label": "field_14", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-p3", "label": "p3", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_10", "label": "field_10", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_14_1", "label": "field_14_1", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-p1", "label": "p1", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-l", "label": "l", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_9", "label": "field_9", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-p2_p4", "label": "p2_p4", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-l", "label": "l", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_18", "label": "field_18", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_19", "label": "field_19", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_20", "label": "filed_20", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-g", "label": "g", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_12", "label": "field_12", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_13", "label": "field_13", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-q", "label": "q", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-v7", "label": "v7", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-f1", "label": "f1", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-f2", "label": "f2", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_7_3", "label": "field_7_3", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_8_1", "label": "field_8_1", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_8_2", "label": "field_8_2", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_8_3", "label": "field_8_3", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-u1", "label": "u1", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-u2", "label": "u2", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-u3", "label": "u3", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-o1", "label": "o1", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-o2", "label": "o2", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-s1", "label": "s1", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-s2", "label": "s2", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_15_1", "label": "field_15_1", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_15_2", "label": "field_15_2", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_15_3", "label": "filed_15_3", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-k", "label": "k", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_6", "label": "field_6", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_17", "label": "field_17", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "edit_car_kba-field_21", "label": "field_21", "class": "edit_car_kba-hidden", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
];

const editCarFormModel = [
    { "name": "edit_car_customer_name", "label": "Customer name:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_ln", "label": "Car license:", "type": "input", "size": "21", "tooltip": "", "check": "edit_car-chk_c_ln", "info": "edit_car-c_ln_info" },
    { "name": "edit_car-c_2", "label": "HSN (2.1):", "type": "input", "size": "21", "tooltip": "", "check": "edit_car-chk_c_2" },
    { "name": "edit_car-c_3", "label": "TSN (2.2):", "type": "input", "size": "21", "tooltip": "", "check": "edit_car-chk_c_3" },
    { "name": "edit_car-c_em", "label": "Emission class (14.1):", "type": "input", "size": "21", "tooltip": "", "check": "edit_car-chk_c_em", "info": "edit_car-c_em_info" },
    { "name": "edit_car-c_d", "label": "Date of registration:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_hu", "label": "Date of HU+AU:", "type": "input", "size": "21", "tooltip": "", "check": "edit_car-chk_c_hu" },
    { "name": "edit_car-c_fin", "label": "FIN:", "type": "input", "size": "21", "tooltip": "", "check": "edit_car-chk_fin" },
    { "name": "edit_car-c_finchk", "label": "FIN check:", "type": "input", "size": "1", "tooltip": "" },
    { "name": "edit_car-c_mkb", "label": "Engine code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_color", "label": "Color code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_gart_list", "label": "Engine code:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "edit_car-c_gart", "label": "Color code:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_st", "label": "Summer wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_wt", "label": "Winter wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_st_l", "label": "LO Sommer wheels:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_wt_l", "label": "LO Winter wheels:", "type": "input", "size": "21", "tooltip": "" },
    //{ "name": "edit_car-flxgr", "label": "Flexrohr size:", "type": "input", "size": "21", "tooltip": "" },  // siehe DB-Tabelle lxc_flex (wird nicht genutzt)
    { "name": "edit_car-c_zrd", "label": "Next ZR change on:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_zrk", "label": "Next ZR change at KM:", "type": "number", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_bf", "label": "Next brake fluid change:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_wd", "label": "Next maintenance service:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_car-c_text", "label": "Comment:", "type": "textarea", "cols": "42", "rows": "5", "tooltip": "Comment" },
    { "name": "edit_car-c_id", "type": "hidden", },
    { "name": "edit_car-kba_id", "type": "hidden", },
    { "name": "car-c_ow", "type": "hidden", },
];

const editKbaFormModel = [
    { "name": "edit_kba-hersteller", "label": "Manufacture:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_kba-fhzart", "label": "Kind:", "type": "select","data": { '': '', 'car': kivi.t8( 'car' ), 'truck': kivi.t8( 'truck' ), 'trailer': kivi.t8( 'trailer' ), 'bike': kivi.t8( 'bike' ), 'tractor': kivi.t8( 'tracktor' ) }, "tooltip":"" }, //selectbox
    { "name": "edit_kba-d2", "label": "Type code (d2):", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_kba-name", "label": "Type name:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_kba-hubraum", "label": "Displacement:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_kba-leistung", "label": "Performance:", "type": "input", "size": "21", "tooltip": ""  },
    { "name": "edit_kba-kraftstoff", "label": "Fuel Type / Content:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_kba-achsen", "label": "Wheelbase:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_kba-masse", "label": "Total weight:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_kba-t", "label": "Vmax:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_kba-field_14_1", "label": "Emission class:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_kba-field_7_1", "label": "Axle load (field_7_1):", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_kba-field_7_2", "label": "Axle load (field_7_2):", "type": "input", "size": "21", "tooltip": "" },
    { "name": "edit_kba-id", "type": "hidden", },
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
   { "name": "search_order-car_license", "label": "Car license:", "type": "input", "size": "21", "tooltip": "" },
   { "name": "search_order-customer_name", "label": "Customer name:", "type": "input", "size": "21", "tooltip": "" },
   { "name": "search_order-car_manuf", "label": "Car manufacturer:", "type": "input", "size": "21", "tooltip": "" },
   { "name": "search_order-car_type", "label": "Car type:", "type": "input", "size": "21", "tooltip": "" },
   { "name": "search_order-car_brand", "label": "Car brand:", "type": "input", "size": "21", "tooltip": "" },
   { "name": "search_order-status", "label": "Status:", "type": "select","data": [], "tooltip":""}, //selectbox
   { "name": "search_order-date_from", "label": "Date from:", "type": "input", "size": "16", "tooltip": "" },
   { "name": "search_order-date_to", "label": "Date to:", "type": "input", "size": "16", "tooltip": "" },
];

const  editNewCustomer = [
    { "name": "edit_new_customer-type", "label": "customer type:", "type": "select","size":"21" ,"data":["Customer", "Vendor"] ,"tooltip": "" },
    { "name": "edit_new_customer-gender", "label": "salutation", "type": "select", "size":"21" ,"data":[], "tooltip": "", },
    { "name": "edit_new_customer-name", "label": "name:", "type": "input", "size": "21", "tooltip": "", "placeholder": "Name" },
    { "name": "edit_new_customer-zipcode", "label": "zipcode: ", "type": "input", "size":"21", "tooltip": "" },
    { "name": "edit_new_customer-location", "label": "location: ", "type": "select","size":"21" ,"data": [], "tooltip": "" },
    { "name": "edit_new_customer-federalstate", "label": "federalstate: ", "size":"21","type": "select", "data": [], "tooltip": ""},
    { "name": "edit_new_customer-street", "label": "street: ", "type": "input","size":"21" ,"tooltip": ""},
    { "name": "edit_new_customer-currency_id", "label": "currency id: ","type": "select","size":"21" ,"data": [], "tooltip": ""},
    { "name": "edit_new_customer-phone_number", "label": "phone: " ,"type": "input", "size": "21" ,"tooltip": "",},
    { "name": "edit_new_customer-email", "label": "E-Mail:", "type": "input", "size":"21", "tooltip": "" },
    { "name": "edit_new_costumer-type", "label": "Customer type:", "type": "select", "data":["Customer", "Supplier"], "tooltip": "" },
    { "name": "edit_new_costumer-gender", "label": "salutation", "type": "select", "data":[""], "tooltip": "" },
    { "name": "edit_new_costumer-name", "label": "name:", "type": "input", "size": "21", "tooltip": "", "placeholder": "Name" },
    { "name": "edit_new_costumer-zipcode", "label": "zipcode", "type": "input", "size":"19", "tooltip": "" },
    { "name": "edit_new_costumer-location", "label": "", "type": "select", "data": [], "tooltip": "" },
    { "name": "edit_new_costumer-federalstate", "label": "", "type": "select", "data": [], "tooltip": ""},
    { "name": "edit_new_costumer-street", "label": "", "type": "input", "tooltip": ""},
]

const editdeladdrCustomer = [
    { "name": "deladdr_Customer-list", "label": "Deliver address:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "deladdr_Customer-shiptoname", "label": "Name:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr_Customer-shiptodepartment_1", "label": "Department 1:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr_Customer-shiptodepartment_2", "label": "Department 2:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr_Customer-shiptozipcode", "label": "Zip code:", "type": "input", "size": "12", "tooltip": "Vollständiger Name" },
    { "name": "deladdr_Customer-shiptobland", "label": "Bundesland:", "type": "select", "data":[], "tooltip":""}, //selectbox
    { "name": "deladdr_Customer-shiptocity", "label": "City:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr_Customer-shiptostreet", "label": "Street:", "type": "input", "size": "42", "tooltip": "Street and street number" },
    { "name": "deladdr_Customer-shiptocountry", "label": "Country code:", "type": "input", "size": "2", "tooltip": "Country code", "data": "D" },
    { "name": "deladdr_Customer-shiptofax", "label": "fax:", "type": "input", "size": "42", "tooltip": "Second phone number" },
    { "name": "deladdr_Customer-shiptocontact", "label": "Contact person:", "type": "input", "size": "42", "tooltip": "Contact person" },
];

const orderEmailFormModel = [
    { "name": "order_email-recipient", "label": "Recipient:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "order_email-subject", "label": "Subject:", "type": "input", "size": "21", "tooltip": "" },
    { "name": "order_email-message", "label": "Message:", "type": "textarea", "cols": "42", "rows": "10" },
    { "name": "order_email-attachment", "label": "Name of attachment:", "type": "input", "size": "21", "tooltip": "" },
];

const showCarDataFormModel = [
    { "name": "show_car_data-hersteller", "label": "Manufacture:", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "show_car_data-typ", "label": "Type:", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "show_car_data-hsn", "label": "HSN:", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "show_car_data-tsn", "label": "TSN:", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "show_car_data-fin", "label": "FIN:", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "show_car_data-hubraum", "label": "Displacement:", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "show_car_data-leistung", "label": "Performance:", "type": "input", "size": "21", "tooltip": "", "readonly": "true" },
    { "name": "show_car_data-c_ln", "type": "hidden", },
];
