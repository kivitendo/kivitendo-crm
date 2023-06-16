--SELECT distinct on ( init_ts, internal_order ) * FROM ( SELECT distinct on ( oe.id, internal_order ) 'true' ::BOOL AS instruction, oe.id,lxc_cars.c_ln, to_char( oe.transdate, 'DD.MM.YYYY') AS transdate, oe.ordnumber, instructions.description, oe.car_status, oe.status, oe.finish_time, customer.name AS owner, oe.c_id AS c_id, oe.customer_id, lxc_cars.c_2 AS c_2, lxc_cars.c_3 AS c_3, oe.car_manuf AS car_manuf, oe.car_type AS car_type, oe.internalorder AS internal_order, oe.itime AS init_ts FROM oe, instructions, parts, lxc_cars, customer WHERE  instructions.trans_id = oe.id AND parts.id = instructions.parts_id AND lxc_cars.c_id = oe.c_id AND customer.id = oe.customer_id UNION SELECT distinct on ( oe.id, internal_order ) 'false'::BOOL AS instruction, oe.id,lxc_cars.c_ln, to_char( oe.transdate, 'DD.MM.YYYY') AS transdate, oe.ordnumber, orderitems.description, oe.car_status, oe.status, oe.finish_time, customer.name AS owner, oe.c_id AS c_id, oe.customer_id, lxc_cars.c_2 AS c_2, lxc_cars.c_3 AS c_3, oe.car_manuf AS car_manuf, oe.car_type AS car_type, oe.internalorder AS internal_order, oe.itime AS init_ts FROM oe, orderitems, parts, lxc_cars, customer WHERE  orderitems.trans_id = oe.id AND parts.id = orderitems.parts_id AND orderitems.position = 1 AND lxc_cars.c_id = oe.c_id AND customer.id = oe.customer_id ORDER BY instruction ASC) AS myTable ORDER BY internal_order ASC, init_ts DESC


--SELECT distinct on ( oe.id, internal_order ) 'true' ::BOOL AS instruction, oe.id,lxc_cars.c_ln, to_char( oe.transdate, 'DD.MM.YYYY') AS transdate, oe.ordnumber, instructions.description, oe.car_status, oe.status, oe.finish_time, customer.name AS owner, oe.c_id AS c_id, oe.customer_id, lxc_cars.c_2 AS c_2, lxc_cars.c_3 AS c_3, oe.car_manuf AS car_manuf, oe.car_type AS car_type, oe.internalorder AS internal_order, oe.itime AS init_ts FROM oe, instructions, parts, lxc_cars, customer JOIN kbacars ON( lxc_cars.c_2 = kbacars.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbacars.tsn   ) WHERE  instructions.trans_id = oe.id AND parts.id = instructions.parts_id AND lxc_cars.c_id = oe.c_id AND customer.id = oe.customer_id


--SELECT DISTINCT ON ( oe.id, customer.name ) customer.name, kbacars.hersteller, kbacars.name, 'automobil' AS mytype, c_ln FROM lxc_cars JOIN kbacars ON( lxc_cars.c_2 = kbacars.hsn AND SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbacars.tsn ) JOIN oe ON( lxc_cars.c_id = oe.c_id ) JOIN customer ON lxc_cars.c_id = oe.c_id WHERE customer.name ILIKE '%Maik Tus%' ORDER BY oe.id;

--KBA---

SELECT oe.id AS oe_id, oe.c_id AS oe_c_id, kbaall.* FROM oe, (
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'automobil' AS mytype FROM lxc_cars JOIN kbacars ON( lxc_cars.c_2 = kbacars.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbacars.tsn   ) UNION All
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'trailer' AS mytype FROM lxc_cars JOIN kbatrailer ON( lxc_cars.c_2 = kbatrailer.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatrailer.tsn   ) UNION ALL
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'bikes' AS mytype FROM lxc_cars JOIN kbabikes ON( lxc_cars.c_2 = kbabikes.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbabikes.tsn   ) UNION ALL
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'trucks' AS mytype FROM lxc_cars JOIN kbatrucks ON( lxc_cars.c_2 = kbatrucks.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatrucks.tsn   ) UNION ALL
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'tractor' AS mytype FROM lxc_cars JOIN kbatractors ON( lxc_cars.c_2 = kbatractors.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatractors.tsn   )
) AS kbaall WHERE oe.c_id = kbaall.c_id;

--Orginal-----------

SELECT distinct on ( init_ts, internal_order ) * FROM (

SELECT distinct on ( oe.id, internal_order ) 'true' ::BOOL AS instruction,
oe.id,lxc_cars.c_ln, to_char( oe.transdate, 'DD.MM.YYYY') AS transdate, oe.ordnumber, instructions.description, oe.car_status, oe.status,
oe.finish_time, customer.name AS owner, oe.c_id AS c_id, oe.customer_id, lxc_cars.c_2 AS c_2, lxc_cars.c_3 AS c_3, oe.car_manuf AS car_manuf, oe.car_type AS car_type,
oe.internalorder AS internal_order, oe.itime AS init_ts
FROM oe, instructions, parts, lxc_cars, customer
WHERE  instructions.trans_id = oe.id AND parts.id = instructions.parts_id AND lxc_cars.c_id = oe.c_id AND customer.id = oe.customer_id

UNION

SELECT distinct on ( oe.id, internal_order ) 'false'::BOOL AS instruction, oe.id,lxc_cars.c_ln, to_char( oe.transdate, 'DD.MM.YYYY') AS transdate, oe.ordnumber,
orderitems.description, oe.car_status, oe.status, oe.finish_time, customer.name AS owner, oe.c_id AS c_id, oe.customer_id, lxc_cars.c_2 AS c_2, lxc_cars.c_3 AS c_3,
oe.car_manuf AS car_manuf, oe.car_type AS car_type, oe.internalorder AS internal_order, oe.itime AS init_ts
FROM oe, orderitems, parts, lxc_cars, customer
WHERE  orderitems.trans_id = oe.id AND parts.id = orderitems.parts_id AND orderitems.position = 1 AND lxc_cars.c_id = oe.c_id AND customer.id = oe.customer_id ORDER BY instruction ASC

) AS myTable ORDER BY internal_order ASC, init_ts DESC;

------------------------------

SELECT distinct on ( init_ts, internal_order ) * FROM (

SELECT distinct on ( oe.id, internal_order ) 'true' ::BOOL AS instruction,
oe.id,lxc_cars.c_ln, to_char( oe.transdate, 'DD.MM.YYYY') AS transdate, oe.ordnumber, instructions.description, oe.car_status, oe.status,
oe.finish_time, customer.name AS owner, oe.c_id AS c_id, oe.customer_id, lxc_cars.c_2 AS c_2, lxc_cars.c_3 AS c_3, kbaall.car_manuf AS car_manuf, kbaall.car_type AS car_type,
oe.internalorder AS internal_order, oe.itime AS init_ts
FROM oe, instructions, parts, lxc_cars, customer, (
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'automobil' AS mytype FROM lxc_cars JOIN kbacars ON( lxc_cars.c_2 = kbacars.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbacars.tsn   ) UNION All
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'trailer' AS mytype FROM lxc_cars JOIN kbatrailer ON( lxc_cars.c_2 = kbatrailer.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatrailer.tsn   ) UNION ALL
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'bikes' AS mytype FROM lxc_cars JOIN kbabikes ON( lxc_cars.c_2 = kbabikes.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbabikes.tsn   ) UNION ALL
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'trucks' AS mytype FROM lxc_cars JOIN kbatrucks ON( lxc_cars.c_2 = kbatrucks.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatrucks.tsn   ) UNION ALL
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'tractor' AS mytype FROM lxc_cars JOIN kbatractors ON( lxc_cars.c_2 = kbatractors.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatractors.tsn   )
) AS kbaall
WHERE oe.c_id = kbaall.c_id AND instructions.trans_id = oe.id AND parts.id = instructions.parts_id AND lxc_cars.c_id = oe.c_id AND customer.id = oe.customer_id

UNION

SELECT distinct on ( oe.id, internal_order ) 'false'::BOOL AS instruction, oe.id,lxc_cars.c_ln, to_char( oe.transdate, 'DD.MM.YYYY') AS transdate, oe.ordnumber,
orderitems.description, oe.car_status, oe.status, oe.finish_time, customer.name AS owner, oe.c_id AS c_id, oe.customer_id, lxc_cars.c_2 AS c_2, lxc_cars.c_3 AS c_3,
kbaall.car_manuf AS car_manuf, kbaall.car_type AS car_type, oe.internalorder AS internal_order, oe.itime AS init_ts
FROM oe, orderitems, parts, lxc_cars, customer, (
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'automobil' AS mytype FROM lxc_cars JOIN kbacars ON( lxc_cars.c_2 = kbacars.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbacars.tsn   ) UNION All
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'trailer' AS mytype FROM lxc_cars JOIN kbatrailer ON( lxc_cars.c_2 = kbatrailer.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatrailer.tsn   ) UNION ALL
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'bikes' AS mytype FROM lxc_cars JOIN kbabikes ON( lxc_cars.c_2 = kbabikes.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbabikes.tsn   ) UNION ALL
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'trucks' AS mytype FROM lxc_cars JOIN kbatrucks ON( lxc_cars.c_2 = kbatrucks.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatrucks.tsn   ) UNION ALL
SELECT c_id, c_ln, hersteller AS car_manuf, name AS car_type, 'tractor' AS mytype FROM lxc_cars JOIN kbatractors ON( lxc_cars.c_2 = kbatractors.hsn AND  SUBSTRING( lxc_cars.c_3, 0, 4 ) = kbatractors.tsn   )
) AS kbaall
WHERE oe.c_id = kbaall.c_id AND orderitems.trans_id = oe.id AND parts.id = orderitems.parts_id AND orderitems.position = 1 AND lxc_cars.c_id = oe.c_id AND customer.id = oe.customer_id ORDER BY instruction ASC

) AS myTable ORDER BY internal_order ASC, init_ts DESC;
