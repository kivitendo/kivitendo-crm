	const billaddrFormModel = [
		{ "name": "billaddr-greetings", "label": "Greetings:", "type": "select", "data":[kivi.t8("Salutation as below"), "Frau", "Herr", "Firma", "Familie", "Dipl.-Ing.", "DR", "Dr.", "Doktor"], "tooltip":""}, //selectbox
		{ "name": "billaddr-greetings-field", "label": "Greetings:", "type": "input", "size": "42", "tooltip": "Alternativ greetings" },
		{ "name": "billaddr-name", "label": "Name:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
		{ "name": "billaddr-street", "label": "Street:", "type": "input", "size": "42", "tooltip": "Street and street number" },
		{ "name": "billaddr-country", "label": "Country:", "type": "input", "size": "2", "tooltip": "Country code" },
		{ "name": "billaddr-bland", "label": "Bundesland:", "type": "select", "data":["", "Baden-Württemberg", "Berlin", "Hamburg", "Mecklenburg-Vorpommern", "Niedersachsen", "Nordrhein-Westfalen", "Rheinland-Pfalz", "Saarland", "Sachsen", "Sachsen-Anhalt", "Schleswig-Holstein", "Thüringen"], "tooltip":""}, //selectbox
		{ "name": "billaddr-phone1", "label": "Phone:", "type": "input", "size": "42", "tooltip": "First phone number" },
		{ "name": "billaddr-phone2", "label": "Phone:", "type": "input", "size": "42", "tooltip": "Second phone number" },
		{ "name": "billaddr-email", "label": "E-Mail:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "billaddr-contact", "label": "Contact person:", "type": "input", "size": "42", "tooltip": "Contact person" },
		{ "name": "billaddr-customer-type", "label": "Costumer type:", "type": "select","data": [], "tooltip":""}, //selectbox
		{ "name": "billaddr-keyword", "label": "Keyword:", "type": "input", "size": "42", "tooltip": "Keyword" },
		{ "name": "billaddr-comment", "label": "Comment:", "type": "textarea", "cols": "42", "rows": "5", "tooltip": "Comment" },
	];

	const deladdrFormModel = [
		{ "name": "deladdr-list", "label": "Deliver address:", "type": "select","data": [], "tooltip":""}, //selectbox
		{ "name": "deladdr-name", "label": "Name:", "type": "input", "size": "42", "tooltip": "Vollständiger Name" },
		{ "name": "deladdr-street", "label": "Street:", "type": "input", "size": "42", "tooltip": "Street and street number" },
		{ "name": "deladdr-country", "label": "Country:", "type": "input", "size": "2", "tooltip": "Country code" },
		{ "name": "deladdr-bland", "label": "Bundesland:", "type": "select", "data":["", "Baden-Württemberg", "Berlin", "Hamburg", "Mecklenburg-Vorpommern", "Niedersachsen", "Nordrhein-Westfalen", "Rheinland-Pfalz", "Saarland", "Sachsen", "Sachsen-Anhalt", "Schleswig-Holstein", "Thüringen"], "tooltip":""}, //selectbox
		{ "name": "deladdr-phone1", "label": "Phone:", "type": "input", "size": "42", "tooltip": "First phone number" },
		{ "name": "deladdr-phone2", "label": "Phone:", "type": "input", "size": "42", "tooltip": "Second phone number" },
		{ "name": "deladdr-email", "label": "E-Mail:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "deladdr-contact", "label": "Contact person:", "type": "input", "size": "42", "tooltip": "Contact person" },
	];

	const banktaxFormModel = [
		{ "name": "banktax-ustid", "label": "UStId:", "type": "select","data": [], "tooltip":""}, //selectbox
		{ "name": "banktax-id", "label": "Tax number:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "banktax-bankname", "label": "Bank name:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "banktax-blz", "label": "BLZ:", "type": "input", "size": "42", "tooltip": "Country code" },
		{ "name": "banktax-account", "label": "Account:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "banktax-iban", "label": "IBAN:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "banktax-bic", "label": "BIC:", "type": "input", "size": "42", "tooltip": "" },
	];

	const extraFormModel = [
		{ "name": "extras-industry", "label": "Industry:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "extras-homepage", "label": "Homepage:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "extras-dep1", "label": "Department:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "extras-dep2", "label": "Department:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "extras-lead1", "label": "Lead source:", "type": "select","data": [], "tooltip":""}, //selectbox
		{ "name": "extras-lead2", "label": "Lead source:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "extras-tax-zone", "label": "Tax zone:", "type": "select","data": [], "tooltip":""}, //selectbox
		{ "name": "extras-payment-terms", "label": "Terms of payment:", "type": "select","data": [], "tooltip":""}, //selectbox
		{ "name": "extras-employ-amount", "label": "Amount of employees:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "extras-group", "label": "Busines group:", "type": "input", "size": "42", "tooltip": "" },
		{ "name": "extras-salesperson", "label": "Salesperson:", "type": "select","data": [], "tooltip":""}, //selectbox
		{ "name": "extras-lang", "label": "Language:", "type": "select","data": [], "tooltip":""}, //selectbox
		{ "name": "extras-salesperson", "label": "Salesperson:", "type": "select","data": [], "tooltip":""}, //selectbox
		{ "name": "extras-permission", "label": "Permission:", "type": "select","data": [], "tooltip":""}, //selectbox
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
