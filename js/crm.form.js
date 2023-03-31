
const billaddrFormModel = [
        { "name": "billaddr-greetings", "label": "Greetings:", "type": "select", "data":[], "tooltip":""}, //selectbox
        { "name": "billaddr-greeting", "label": "Greetings:", "type": "input", "size": "42", "tooltip": "Alternativ greetings" },
        { "name": "billaddr-name", "label": "Name:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
        { "name": "billaddr-street", "label": "Street:", "type": "input", "size": "42", "tooltip": "Street and street number" },
        { "name": "billaddr-country", "label": "Country code:", "type": "input", "size": "4", "tooltip": "Country code" },
        { "name": "billaddr-zipcode", "label": "Zip code:", "type": "input", "size": "12", "tooltip": "Zip code" },
        { "name": "billaddr-bland", "label": "Bundesland:", "type": "select", "data":[], "tooltip":""}, //selectbox
        { "name": "billaddr-phone", "label": "Phone:", "type": "input", "size": "42", "tooltip": "First phone number" },
        { "name": "billaddr-fax", "label": "Phone:", "type": "input", "size": "42", "tooltip": "Second phone number" },
        { "name": "billaddr-email", "label": "E-Mail:", "type": "input", "size": "42", "tooltip": "" },
        { "name": "billaddr-contact", "label": "Contact person:", "type": "input", "size": "42", "tooltip": "Contact person" },
        { "name": "billaddr-business", "label": "Costumer type:", "type": "select","data": [], "tooltip":""}, //selectbox
        { "name": "billaddr-sw", "label": "Keyword:", "type": "input", "size": "42", "tooltip": "Keyword" },
        { "name": "billaddr-notes", "label": "Comment:", "type": "textarea", "cols": "42", "rows": "5", "tooltip": "Comment" },
    ];

const deladdrFormModel = [
    { "name": "deladdr-list", "label": "Deliver address:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "deladdr-shiptoname", "label": "Name:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptodepartment_1", "label": "Department 1:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptodepartment_2", "label": "Department 2:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
    { "name": "deladdr-shiptostreet", "label": "Street:", "type": "input", "size": "42", "tooltip": "Street and street number" },
    { "name": "deladdr-shiptocountry", "label": "Country code:", "type": "input", "size": "2", "tooltip": "Country code" },
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
    { "name": "billaddr-tax_zone", "label": "Tax zone:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "billaddr-payment_terms", "label": "Terms of payment:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "billaddr-headcount", "label": "Amount of employees:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-konzern", "label": "Busines group:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "billaddr-lang", "label": "Language:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "billaddr-salesperson", "label": "Salesperson:", "type": "select","data": [], "tooltip":""}, //selectbox
];

const varsFormModel = [
    { "name": "vars-onlineshop", "label": "Online-Shop:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "vars-user-name", "label": "User name:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "vars-opening-hours", "label": "Opening hours:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "vars-birthsday", "label": "Birthsday:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "vars-behavior", "label": "Behavior:", "type": "select","data": [], "tooltip":""}, //selectbox
    { "name": "vars-custnum", "label": "Customer number:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "vars-birthsday", "label": "Birthsday:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "vars-passwd", "label": "Password:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "vars-insurance", "label": "Insurance:", "type": "input", "size": "42", "tooltip": "" },
    { "name": "vars-tip", "label": "Tip:", "type": "select","data": [], "tooltip":""}, //selectbox
];
