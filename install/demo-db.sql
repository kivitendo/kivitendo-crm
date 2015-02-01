--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: tax; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA tax;


ALTER SCHEMA tax OWNER TO postgres;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: chart_category_to_sgn(character); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION chart_category_to_sgn(character) RETURNS integer
    LANGUAGE sql
    AS $_$SELECT  1 WHERE $1 IN ('I', 'L', 'Q')
      UNION 
    SELECT -1 WHERE $1 IN ('E', 'A')$_$;


ALTER FUNCTION public.chart_category_to_sgn(character) OWNER TO postgres;

--
-- Name: check_bin_belongs_to_wh(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION check_bin_belongs_to_wh() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
        IF NEW.bin_id IS NULL AND NEW.warehouse_id IS NULL THEN
          RETURN NEW;
        END IF;
        IF NEW.bin_id IN (SELECT id FROM bin WHERE warehouse_id = NEW.warehouse_id) THEN
          RETURN NEW;
        ELSE
          RAISE EXCEPTION 'bin (id=%) does not belong to warehouse (id=%).', NEW.bin_id, NEW.warehouse_id;
          RETURN NULL;
        END IF;
      END;$$;


ALTER FUNCTION public.check_bin_belongs_to_wh() OWNER TO postgres;

--
-- Name: check_inventory(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION check_inventory() RETURNS trigger
    LANGUAGE plpgsql
    AS $$declare  itemid int;  row_data inventory%rowtype;begin  if not old.quotation then    for row_data in select * from inventory where oe_id = old.id loop      select into itemid id from orderitems where trans_id = old.id and id = row_data.orderitems_id;      if itemid is null then	delete from inventory where oe_id = old.id and orderitems_id = row_data.orderitems_id;      end if;    end loop;  end if;  return old;end;$$;


ALTER FUNCTION public.check_inventory() OWNER TO postgres;

--
-- Name: clean_up_acc_trans_after_ar_ap_gl_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION clean_up_acc_trans_after_ar_ap_gl_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM acc_trans WHERE trans_id = OLD.id;
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.clean_up_acc_trans_after_ar_ap_gl_delete() OWNER TO postgres;

--
-- Name: clean_up_after_customer_vendor_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION clean_up_after_customer_vendor_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM contacts
    WHERE cp_cv_id = OLD.id;

    DELETE FROM shipto
    WHERE (trans_id = OLD.id)
      AND (module   = 'CT');

    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.clean_up_after_customer_vendor_delete() OWNER TO postgres;

--
-- Name: clean_up_record_links_before_ap_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION clean_up_record_links_before_ap_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM record_links
      WHERE (from_table = 'ap' AND from_id = OLD.id)
         OR (to_table   = 'ap' AND to_id   = OLD.id);
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.clean_up_record_links_before_ap_delete() OWNER TO postgres;

--
-- Name: clean_up_record_links_before_ar_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION clean_up_record_links_before_ar_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM record_links
      WHERE (from_table = 'ar' AND from_id = OLD.id)
         OR (to_table   = 'ar' AND to_id   = OLD.id);
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.clean_up_record_links_before_ar_delete() OWNER TO postgres;

--
-- Name: clean_up_record_links_before_delivery_order_items_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION clean_up_record_links_before_delivery_order_items_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM record_links
      WHERE (from_table = 'delivery_order_items' AND from_id = OLD.id)
         OR (to_table   = 'delivery_order_items' AND to_id   = OLD.id);
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.clean_up_record_links_before_delivery_order_items_delete() OWNER TO postgres;

--
-- Name: clean_up_record_links_before_delivery_orders_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION clean_up_record_links_before_delivery_orders_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM record_links
      WHERE (from_table = 'delivery_orders' AND from_id = OLD.id)
         OR (to_table   = 'delivery_orders' AND to_id   = OLD.id);
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.clean_up_record_links_before_delivery_orders_delete() OWNER TO postgres;

--
-- Name: clean_up_record_links_before_invoice_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION clean_up_record_links_before_invoice_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM record_links
      WHERE (from_table = 'invoice' AND from_id = OLD.id)
         OR (to_table   = 'invoice' AND to_id   = OLD.id);
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.clean_up_record_links_before_invoice_delete() OWNER TO postgres;

--
-- Name: clean_up_record_links_before_oe_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION clean_up_record_links_before_oe_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM record_links
      WHERE (from_table = 'oe' AND from_id = OLD.id)
         OR (to_table   = 'oe' AND to_id   = OLD.id);
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.clean_up_record_links_before_oe_delete() OWNER TO postgres;

--
-- Name: clean_up_record_links_before_orderitems_delete(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION clean_up_record_links_before_orderitems_delete() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM record_links
      WHERE (from_table = 'orderitems' AND from_id = OLD.id)
         OR (to_table   = 'orderitems' AND to_id   = OLD.id);
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.clean_up_record_links_before_orderitems_delete() OWNER TO postgres;

--
-- Name: comma_aggregate(text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION comma_aggregate(text, text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
  SELECT CASE WHEN $1 <> '' THEN $1 || ', ' || $2 
                              ELSE $2 
         END; 
$_$;


ALTER FUNCTION public.comma_aggregate(text, text) OWNER TO postgres;

--
-- Name: delete_custom_variables_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION delete_custom_variables_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    IF (TG_TABLE_NAME IN ('orderitems', 'delivery_order_items', 'invoice')) THEN
      PERFORM delete_custom_variables_with_sub_module('IC', TG_TABLE_NAME, old.id);
    END IF;

    IF (TG_TABLE_NAME = 'parts') THEN
      PERFORM delete_custom_variables_with_sub_module('IC', '', old.id);
    END IF;

    IF (TG_TABLE_NAME IN ('customer', 'vendor')) THEN
      PERFORM delete_custom_variables_with_sub_module('CT', '', old.id);
    END IF;

    IF (TG_TABLE_NAME = 'contacts') THEN
      PERFORM delete_custom_variables_with_sub_module('Contacts', '', old.cp_id);
    END IF;

    IF (TG_TABLE_NAME = 'project') THEN
      PERFORM delete_custom_variables_with_sub_module('Projects', '', old.id);
    END IF;

    RETURN old;
  END;
$$;


ALTER FUNCTION public.delete_custom_variables_trigger() OWNER TO postgres;

--
-- Name: delete_custom_variables_with_sub_module(text, text, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION delete_custom_variables_with_sub_module(config_module text, cvar_sub_module text, old_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM custom_variables
    WHERE EXISTS (SELECT id FROM custom_variable_configs cfg WHERE (cfg.module = config_module) AND (custom_variables.config_id = cfg.id))
      AND (COALESCE(sub_module, '') = cvar_sub_module)
      AND (trans_id                 = old_id);

    RETURN TRUE;
  END;
$$;


ALTER FUNCTION public.delete_custom_variables_with_sub_module(config_module text, cvar_sub_module text, old_id integer) OWNER TO postgres;

--
-- Name: delivery_orders_before_delete_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION delivery_orders_before_delete_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
        BEGIN
          DELETE FROM status                     WHERE trans_id = OLD.id;
          DELETE FROM delivery_order_items_stock WHERE delivery_order_item_id IN (SELECT id FROM delivery_order_items WHERE delivery_order_id = OLD.id);
          DELETE FROM shipto                     WHERE (trans_id = OLD.id) AND (module = 'OE');

          RETURN OLD;
        END;
      $$;


ALTER FUNCTION public.delivery_orders_before_delete_trigger() OWNER TO postgres;

--
-- Name: follow_up_close_when_oe_closed_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION follow_up_close_when_oe_closed_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    IF COALESCE(NEW.closed, FALSE) AND NOT COALESCE(OLD.closed, FALSE) THEN
      UPDATE follow_ups
      SET done = TRUE
      WHERE id IN (
        SELECT follow_up_id
        FROM follow_up_links
        WHERE (trans_id   = NEW.id)
          AND (trans_type IN ('sales_quotation',   'sales_order',    'sales_delivery_order',
                              'request_quotation', 'purchase_order', 'purchase_delivery_order'))
      );
    END IF;

    RETURN NEW;
  END;
$$;


ALTER FUNCTION public.follow_up_close_when_oe_closed_trigger() OWNER TO postgres;

--
-- Name: follow_up_delete_notes_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION follow_up_delete_notes_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM notes
    WHERE (trans_id     = OLD.id)
      AND (trans_module = 'fu');
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.follow_up_delete_notes_trigger() OWNER TO postgres;

--
-- Name: follow_up_delete_when_customer_vendor_is_deleted_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION follow_up_delete_when_customer_vendor_is_deleted_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM follow_ups
    WHERE id IN (
      SELECT follow_up_id
      FROM follow_up_links
      WHERE (trans_id   = OLD.id)
        AND (trans_type IN ('customer', 'vendor'))
    );

    DELETE FROM notes
    WHERE (trans_id     = OLD.id)
      AND (trans_module = 'ct');

    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.follow_up_delete_when_customer_vendor_is_deleted_trigger() OWNER TO postgres;

--
-- Name: follow_up_delete_when_oe_is_deleted_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION follow_up_delete_when_oe_is_deleted_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM follow_ups
    WHERE id IN (
      SELECT follow_up_id
      FROM follow_up_links
      WHERE (trans_id   = OLD.id)
        AND (trans_type IN ('sales_quotation',   'sales_order',    'sales_delivery_order',    'sales_invoice',
                            'request_quotation', 'purchase_order', 'purchase_delivery_order', 'purchase_invoice'))
    );

    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.follow_up_delete_when_oe_is_deleted_trigger() OWNER TO postgres;

--
-- Name: generic_translations_delete_on_delivery_terms_delete_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION generic_translations_delete_on_delivery_terms_delete_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM generic_translations
      WHERE translation_id = OLD.id AND translation_type LIKE 'SL::DB::DeliveryTerm/description_long';
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.generic_translations_delete_on_delivery_terms_delete_trigger() OWNER TO postgres;

--
-- Name: generic_translations_delete_on_payment_terms_delete_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION generic_translations_delete_on_payment_terms_delete_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM generic_translations
      WHERE translation_id = OLD.id AND translation_type LIKE 'SL::DB::PaymentTerm/description_long';
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.generic_translations_delete_on_payment_terms_delete_trigger() OWNER TO postgres;

--
-- Name: generic_translations_delete_on_tax_delete_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION generic_translations_delete_on_tax_delete_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    DELETE FROM generic_translations
      WHERE translation_id = OLD.id AND translation_type LIKE 'SL::DB::Tax/taxdescription';
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.generic_translations_delete_on_tax_delete_trigger() OWNER TO postgres;

--
-- Name: idx(anyarray, anyelement); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION idx(anyarray, anyelement) RETURNS integer
    LANGUAGE sql IMMUTABLE
    AS $_$ SELECT i FROM ( SELECT generate_series(array_lower($1,1),array_upper($1,1)) ) g(i) WHERE $1[i] = $2 LIMIT 1; $_$;


ALTER FUNCTION public.idx(anyarray, anyelement) OWNER TO postgres;

--
-- Name: oe_before_delete_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION oe_before_delete_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
        BEGIN
          DELETE FROM status WHERE trans_id = OLD.id;
          DELETE FROM shipto WHERE (trans_id = OLD.id) AND (module = 'OE');

          RETURN OLD;
        END;
      $$;


ALTER FUNCTION public.oe_before_delete_trigger() OWNER TO postgres;

--
-- Name: recalculate_all_spec_item_time_estimations(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION recalculate_all_spec_item_time_estimations() RETURNS boolean
    LANGUAGE plpgsql
    AS $$
  DECLARE
    rspec RECORD;
  BEGIN
    FOR rspec IN SELECT id FROM requirement_specs LOOP
      PERFORM recalculate_spec_item_time_estimation(rspec.id);
    END LOOP;

    RETURN TRUE;
  END;
$$;


ALTER FUNCTION public.recalculate_all_spec_item_time_estimations() OWNER TO postgres;

--
-- Name: recalculate_spec_item_time_estimation(integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION recalculate_spec_item_time_estimation(the_requirement_spec_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
  DECLARE
    item RECORD;
  BEGIN
    FOR item IN
      SELECT DISTINCT parent_id
      FROM requirement_spec_items
      WHERE (requirement_spec_id = the_requirement_spec_id)
        AND (item_type           = 'sub-function-block')
    LOOP
      RAISE DEBUG 'hmm function-block with sub: %', item.parent_id;
      PERFORM update_requirement_spec_item_time_estimation(item.parent_id, the_requirement_spec_id);
    END LOOP;

    FOR item IN
      SELECT DISTINCT parent_id
      FROM requirement_spec_items
      WHERE (requirement_spec_id = the_requirement_spec_id)
        AND (item_type           = 'function-block')
        AND (id NOT IN (
          SELECT parent_id
          FROM requirement_spec_items
          WHERE (requirement_spec_id = the_requirement_spec_id)
            AND (item_type           = 'sub-function-block')
        ))
    LOOP
      RAISE DEBUG 'hmm section with function-block: %', item.parent_id;
      PERFORM update_requirement_spec_item_time_estimation(item.parent_id, the_requirement_spec_id);
    END LOOP;

    PERFORM update_requirement_spec_item_time_estimation(NULL, the_requirement_spec_id);

    RETURN TRUE;
  END;
$$;


ALTER FUNCTION public.recalculate_spec_item_time_estimation(the_requirement_spec_id integer) OWNER TO postgres;

--
-- Name: requirement_spec_delete_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION requirement_spec_delete_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    IF TG_WHEN = 'AFTER' THEN
      DELETE FROM trigger_information WHERE (key = 'deleting_requirement_spec') AND (value = CAST(OLD.id AS TEXT));

      RETURN OLD;
    END IF;

    RAISE DEBUG 'before delete trigger on %', OLD.id;

    INSERT INTO trigger_information (key, value) VALUES ('deleting_requirement_spec', CAST(OLD.id AS TEXT));

    RAISE DEBUG '  Converting items into sections items for %', OLD.id;
    UPDATE requirement_spec_items SET item_type  = 'section', parent_id = NULL WHERE requirement_spec_id = OLD.id;

    RAISE DEBUG '  And we out for %', OLD.id;

    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.requirement_spec_delete_trigger() OWNER TO postgres;

--
-- Name: requirement_spec_item_before_delete_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION requirement_spec_item_before_delete_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  BEGIN
    RAISE DEBUG 'delete trig RSitem old id %', OLD.id;
    INSERT INTO trigger_information (key, value) VALUES ('deleting_requirement_spec_item', CAST(OLD.id AS TEXT));
    DELETE FROM requirement_spec_items WHERE (parent_id         = OLD.id);
    DELETE FROM trigger_information    WHERE (key = 'deleting_requirement_spec_item') AND (value = CAST(OLD.id AS TEXT));
    RAISE DEBUG 'delete trig END %', OLD.id;
    RETURN OLD;
  END;
$$;


ALTER FUNCTION public.requirement_spec_item_before_delete_trigger() OWNER TO postgres;

--
-- Name: requirement_spec_item_time_estimation_updater_trigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION requirement_spec_item_time_estimation_updater_trigger() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
  DECLARE
    do_new BOOLEAN;
  BEGIN
    RAISE DEBUG 'updateRSITE op %', TG_OP;
    IF ((TG_OP = 'UPDATE') OR (TG_OP = 'DELETE')) THEN
      RAISE DEBUG 'UPDATE trigg op % OLD.id % OLD.parent_id %', TG_OP, OLD.id, OLD.parent_id;
      PERFORM update_requirement_spec_item_time_estimation(OLD.parent_id, OLD.requirement_spec_id);
      RAISE DEBUG 'UPDATE trigg op % END %', TG_OP, OLD.id;
    END IF;
    do_new = FALSE;

    IF (TG_OP = 'UPDATE') THEN
      do_new = OLD.parent_id <> NEW.parent_id;
    END IF;

    IF (do_new OR (TG_OP = 'INSERT')) THEN
      RAISE DEBUG 'UPDATE trigg op % NEW.id % NEW.parent_id %', TG_OP, NEW.id, NEW.parent_id;
      PERFORM update_requirement_spec_item_time_estimation(NEW.parent_id, NEW.requirement_spec_id);
      RAISE DEBUG 'UPDATE trigg op % END %', TG_OP, NEW.id;
    END IF;

    RETURN NULL;
  END;
$$;


ALTER FUNCTION public.requirement_spec_item_time_estimation_updater_trigger() OWNER TO postgres;

--
-- Name: set_mtime(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION set_mtime() RETURNS trigger
    LANGUAGE plpgsql
    AS $$    BEGIN        NEW.mtime := 'now';        RETURN NEW;    END;$$;


ALTER FUNCTION public.set_mtime() OWNER TO postgres;

--
-- Name: set_priceupdate_parts(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION set_priceupdate_parts() RETURNS trigger
    LANGUAGE plpgsql
    AS $$    BEGIN        NEW.priceupdate := 'now';        RETURN NEW;    END;$$;


ALTER FUNCTION public.set_priceupdate_parts() OWNER TO postgres;

--
-- Name: update_onhand(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION update_onhand() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  IF tg_op = 'INSERT' THEN
    UPDATE parts SET onhand = COALESCE(onhand, 0) + new.qty WHERE id = new.parts_id;
    RETURN new;
  ELSIF tg_op = 'DELETE' THEN
    UPDATE parts SET onhand = COALESCE(onhand, 0) - old.qty WHERE id = old.parts_id;
    RETURN old;
  ELSE
    UPDATE parts SET onhand = COALESCE(onhand, 0) - old.qty + new.qty WHERE id = old.parts_id;
    RETURN new;
  END IF;
END;
$$;


ALTER FUNCTION public.update_onhand() OWNER TO postgres;

--
-- Name: update_purchase_price(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION update_purchase_price() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  if tg_op = 'DELETE' THEN
    UPDATE parts SET lastcost = COALESCE((select sum ((a.qty * (p.lastcost / COALESCE(pf.factor,
    1)))) as summe from assembly a left join parts p on (p.id = a.parts_id) 
    LEFT JOIN price_factors pf on (p.price_factor_id = pf.id) where a.id = parts.id),0) 
    WHERE assembly = TRUE and id = old.id;
    return old;	
  ELSE
    UPDATE parts SET lastcost = COALESCE((select sum ((a.qty * (p.lastcost / COALESCE(pf.factor, 
    1)))) as summe from assembly a left join parts p on (p.id = a.parts_id)
    LEFT JOIN price_factors pf on (p.price_factor_id = pf.id) 
    WHERE a.id = parts.id),0) where assembly = TRUE and id = new.id;
    return new; 
		
  END IF;
END;
$$;


ALTER FUNCTION public.update_purchase_price() OWNER TO postgres;

--
-- Name: update_requirement_spec_item_time_estimation(integer, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION update_requirement_spec_item_time_estimation(item_id integer, item_requirement_spec_id integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $$
  DECLARE
    current_row RECORD;
    new_row     RECORD;
  BEGIN
    IF EXISTS(
      SELECT *
      FROM trigger_information
      WHERE ((key = 'deleting_requirement_spec_item') AND (value = CAST(item_id                  AS TEXT)))
         OR ((key = 'deleting_requirement_spec')      AND (value = CAST(item_requirement_spec_id AS TEXT)))
      LIMIT 1
    ) THEN
      RAISE DEBUG 'updateRSIE: item_id % or requirement_spec_id % is about to be deleted; do not update', item_id, item_requirement_spec_id;
      RETURN FALSE;
    END IF;

    
    
    IF item_id IS NULL THEN
      SELECT COALESCE(time_estimation, 0) AS time_estimation
      INTO current_row
      FROM requirement_specs
      WHERE id = item_requirement_spec_id;

      SELECT COALESCE(SUM(time_estimation), 0) AS time_estimation
      INTO new_row
      FROM requirement_spec_items
      WHERE (parent_id IS NULL)
        AND (requirement_spec_id = item_requirement_spec_id);

      IF current_row.time_estimation <> new_row.time_estimation THEN
        RAISE DEBUG 'updateRSIE: updating requirement_spec % itself: old estimation % new %.', item_requirement_spec_id, current_row.time_estimation, new_row.time_estimation;

        UPDATE requirement_specs
        SET time_estimation = new_row.time_estimation
        WHERE id = item_requirement_spec_id;
      END IF;

      RETURN TRUE;
    END IF;

    
    
    
    

    SELECT COALESCE(time_estimation, 0) AS time_estimation
    INTO current_row
    FROM requirement_spec_items
    WHERE id = item_id;

    SELECT COALESCE(SUM(time_estimation), 0) AS time_estimation
    INTO new_row
    FROM requirement_spec_items
    WHERE (parent_id = item_id);

    IF current_row.time_estimation = new_row.time_estimation THEN
      RAISE DEBUG 'updateRSIE: item %: nothing to do', item_id;
      RETURN TRUE;
    END IF;

    RAISE DEBUG 'updateRSIE: updating item %: old estimation % new %.', item_id, current_row.time_estimation, new_row.time_estimation;

    UPDATE requirement_spec_items
    SET time_estimation = new_row.time_estimation
    WHERE id = item_id;

    RETURN TRUE;
  END;
$$;


ALTER FUNCTION public.update_requirement_spec_item_time_estimation(item_id integer, item_requirement_spec_id integer) OWNER TO postgres;

--
-- Name: comma(text); Type: AGGREGATE; Schema: public; Owner: postgres
--

CREATE AGGREGATE comma(text) (
    SFUNC = comma_aggregate,
    STYPE = text,
    INITCOND = ''
);


ALTER AGGREGATE public.comma(text) OWNER TO postgres;

--
-- Name: acc_trans_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE acc_trans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.acc_trans_id_seq OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: acc_trans; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE acc_trans (
    acc_trans_id bigint DEFAULT nextval('acc_trans_id_seq'::regclass) NOT NULL,
    trans_id integer NOT NULL,
    chart_id integer NOT NULL,
    amount numeric(15,5),
    transdate date DEFAULT ('now'::text)::date,
    gldate date DEFAULT ('now'::text)::date,
    source text,
    cleared boolean DEFAULT false NOT NULL,
    fx_transaction boolean DEFAULT false NOT NULL,
    ob_transaction boolean DEFAULT false NOT NULL,
    cb_transaction boolean DEFAULT false NOT NULL,
    project_id integer,
    memo text,
    taxkey integer,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    chart_link text NOT NULL,
    tax_id integer NOT NULL
);


ALTER TABLE public.acc_trans OWNER TO postgres;

--
-- Name: ap; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ap (
    id integer DEFAULT nextval(('glid'::text)::regclass) NOT NULL,
    invnumber text NOT NULL,
    transdate date DEFAULT ('now'::text)::date,
    gldate date DEFAULT ('now'::text)::date,
    vendor_id integer,
    taxincluded boolean DEFAULT false,
    amount numeric(15,5),
    netamount numeric(15,5),
    paid numeric(15,5),
    datepaid date,
    duedate date,
    invoice boolean DEFAULT false,
    ordnumber text,
    notes text,
    employee_id integer,
    quonumber text,
    intnotes text,
    department_id integer,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    shipvia text,
    cp_id integer,
    language_id integer,
    payment_id integer,
    storno boolean DEFAULT false,
    taxzone_id integer NOT NULL,
    type text,
    orddate date,
    quodate date,
    globalproject_id integer,
    storno_id integer,
    transaction_description text,
    direct_debit boolean DEFAULT false,
    deliverydate date,
    delivery_term_id integer,
    currency_id integer NOT NULL
);


ALTER TABLE public.ap OWNER TO postgres;

--
-- Name: ar; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE ar (
    id integer DEFAULT nextval(('glid'::text)::regclass) NOT NULL,
    invnumber text NOT NULL,
    transdate date DEFAULT ('now'::text)::date,
    gldate date DEFAULT ('now'::text)::date,
    customer_id integer,
    taxincluded boolean,
    amount numeric(15,5),
    netamount numeric(15,5),
    paid numeric(15,5),
    datepaid date,
    duedate date,
    deliverydate date,
    invoice boolean DEFAULT false,
    shippingpoint text,
    terms smallint DEFAULT 0,
    notes text,
    ordnumber text,
    employee_id integer,
    quonumber text,
    cusordnumber text,
    intnotes text,
    department_id integer,
    shipvia text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    cp_id integer,
    language_id integer,
    payment_id integer,
    delivery_customer_id integer,
    delivery_vendor_id integer,
    storno boolean DEFAULT false,
    taxzone_id integer NOT NULL,
    shipto_id integer,
    type text,
    dunning_config_id integer,
    orddate date,
    quodate date,
    globalproject_id integer,
    salesman_id integer,
    marge_total numeric(15,5),
    marge_percent numeric(15,5),
    storno_id integer,
    transaction_description text,
    donumber text,
    invnumber_for_credit_note text,
    direct_debit boolean DEFAULT false,
    delivery_term_id integer,
    currency_id integer NOT NULL
);


ALTER TABLE public.ar OWNER TO postgres;

--
-- Name: assembly_assembly_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE assembly_assembly_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.assembly_assembly_id_seq OWNER TO postgres;

SET default_with_oids = true;

--
-- Name: assembly; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE assembly (
    id integer,
    parts_id integer,
    qty real,
    bom boolean,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    assembly_id integer DEFAULT nextval('assembly_assembly_id_seq'::regclass) NOT NULL
);


ALTER TABLE public.assembly OWNER TO postgres;

SET default_with_oids = false;

--
-- Name: background_job_histories; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE background_job_histories (
    id integer NOT NULL,
    package_name character varying(255),
    run_at timestamp without time zone,
    status character varying(255),
    result text,
    error text,
    data text
);


ALTER TABLE public.background_job_histories OWNER TO postgres;

--
-- Name: background_job_histories_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE background_job_histories_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.background_job_histories_id_seq OWNER TO postgres;

--
-- Name: background_job_histories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE background_job_histories_id_seq OWNED BY background_job_histories.id;


--
-- Name: background_jobs; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE background_jobs (
    id integer NOT NULL,
    type character varying(255),
    package_name character varying(255),
    last_run_at timestamp without time zone,
    next_run_at timestamp without time zone,
    data text,
    active boolean,
    cron_spec character varying(255)
);


ALTER TABLE public.background_jobs OWNER TO postgres;

--
-- Name: background_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE background_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.background_jobs_id_seq OWNER TO postgres;

--
-- Name: background_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE background_jobs_id_seq OWNED BY background_jobs.id;


--
-- Name: id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 2147483647
    CACHE 1;


ALTER TABLE public.id OWNER TO postgres;

--
-- Name: bank_accounts; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE bank_accounts (
    id integer DEFAULT nextval('id'::regclass) NOT NULL,
    account_number character varying(100),
    bank_code character varying(100),
    iban character varying(100),
    bic character varying(100),
    bank text,
    chart_id integer NOT NULL,
    name text
);


ALTER TABLE public.bank_accounts OWNER TO postgres;

--
-- Name: bin; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE bin (
    id integer DEFAULT nextval('id'::regclass) NOT NULL,
    warehouse_id integer NOT NULL,
    description text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.bin OWNER TO postgres;

--
-- Name: buchungsgruppen; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE buchungsgruppen (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    description text,
    inventory_accno_id integer,
    sortkey integer NOT NULL
);


ALTER TABLE public.buchungsgruppen OWNER TO postgres;

--
-- Name: bundesland; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE bundesland (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    country character(3),
    bundesland character varying(50)
);


ALTER TABLE public.bundesland OWNER TO postgres;

--
-- Name: business; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE business (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    description text,
    discount real,
    customernumberinit text,
    salesman boolean DEFAULT false,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.business OWNER TO postgres;

--
-- Name: chart; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE chart (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    accno text NOT NULL,
    description text,
    charttype character(1) DEFAULT 'A'::bpchar,
    category character(1),
    link text NOT NULL,
    taxkey_id integer,
    pos_bwa integer,
    pos_bilanz integer,
    pos_eur integer,
    datevautomatik boolean DEFAULT false,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    new_chart_id integer,
    valid_from date
);


ALTER TABLE public.chart OWNER TO postgres;

--
-- Name: contacts; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE contacts (
    cp_id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    cp_cv_id integer,
    cp_title text,
    cp_givenname text,
    cp_name text,
    cp_email text,
    cp_phone1 text,
    cp_phone2 text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    cp_fax text,
    cp_mobile1 text,
    cp_mobile2 text,
    cp_satphone text,
    cp_satfax text,
    cp_project text,
    cp_privatphone text,
    cp_privatemail text,
    cp_abteilung text,
    cp_gender character(1),
    cp_street text,
    cp_zipcode text,
    cp_city text,
    cp_birthday date,
    cp_position text,
    cp_homepage text,
    cp_notes text,
    cp_beziehung integer,
    cp_sonder integer,
    cp_stichwort1 text,
    cp_owener integer,
    cp_employee integer,
    cp_grafik character varying(5),
    cp_country character varying(3),
    cp_salutation text
);


ALTER TABLE public.contacts OWNER TO postgres;

--
-- Name: contmasch; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE contmasch (
    mid integer,
    cid integer
);


ALTER TABLE public.contmasch OWNER TO postgres;

--
-- Name: contract; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE contract (
    cid integer DEFAULT nextval(('crmid'::text)::regclass),
    contractnumber text,
    template text,
    bemerkung text,
    customer_id integer,
    anfangdatum date,
    betrag numeric(15,5),
    endedatum date
);


ALTER TABLE public.contract OWNER TO postgres;

--
-- Name: crm; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE crm (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    uid integer,
    datum timestamp without time zone,
    version character(5)
);


ALTER TABLE public.crm OWNER TO postgres;

--
-- Name: crmdefaults; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE crmdefaults (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    employee integer DEFAULT (-1) NOT NULL,
    key text,
    val text,
    grp character(10),
    modify timestamp without time zone DEFAULT now()
);


ALTER TABLE public.crmdefaults OWNER TO postgres;

--
-- Name: crmemployee; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE crmemployee (
    manid integer NOT NULL,
    uid integer,
    key text,
    val text,
    typ character(1) DEFAULT 't'::bpchar
);


ALTER TABLE public.crmemployee OWNER TO postgres;

--
-- Name: crmid; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE crmid
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.crmid OWNER TO postgres;

--
-- Name: csv_import_profile_settings; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE csv_import_profile_settings (
    id integer NOT NULL,
    csv_import_profile_id integer NOT NULL,
    key text NOT NULL,
    value text
);


ALTER TABLE public.csv_import_profile_settings OWNER TO postgres;

--
-- Name: csv_import_profile_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE csv_import_profile_settings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.csv_import_profile_settings_id_seq OWNER TO postgres;

--
-- Name: csv_import_profile_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE csv_import_profile_settings_id_seq OWNED BY csv_import_profile_settings.id;


--
-- Name: csv_import_profiles; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE csv_import_profiles (
    id integer NOT NULL,
    name text NOT NULL,
    type character varying(20) NOT NULL,
    is_default boolean DEFAULT false,
    login text
);


ALTER TABLE public.csv_import_profiles OWNER TO postgres;

--
-- Name: csv_import_profiles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE csv_import_profiles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.csv_import_profiles_id_seq OWNER TO postgres;

--
-- Name: csv_import_profiles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE csv_import_profiles_id_seq OWNED BY csv_import_profiles.id;


--
-- Name: csv_import_report_rows; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE csv_import_report_rows (
    id integer NOT NULL,
    csv_import_report_id integer NOT NULL,
    col integer NOT NULL,
    "row" integer NOT NULL,
    value text
);


ALTER TABLE public.csv_import_report_rows OWNER TO postgres;

--
-- Name: csv_import_report_rows_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE csv_import_report_rows_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.csv_import_report_rows_id_seq OWNER TO postgres;

--
-- Name: csv_import_report_rows_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE csv_import_report_rows_id_seq OWNED BY csv_import_report_rows.id;


--
-- Name: csv_import_report_status; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE csv_import_report_status (
    id integer NOT NULL,
    csv_import_report_id integer NOT NULL,
    "row" integer NOT NULL,
    type text NOT NULL,
    value text
);


ALTER TABLE public.csv_import_report_status OWNER TO postgres;

--
-- Name: csv_import_report_status_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE csv_import_report_status_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.csv_import_report_status_id_seq OWNER TO postgres;

--
-- Name: csv_import_report_status_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE csv_import_report_status_id_seq OWNED BY csv_import_report_status.id;


--
-- Name: csv_import_reports; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE csv_import_reports (
    id integer NOT NULL,
    session_id text NOT NULL,
    profile_id integer NOT NULL,
    type text NOT NULL,
    file text NOT NULL,
    numrows integer NOT NULL,
    numheaders integer NOT NULL
);


ALTER TABLE public.csv_import_reports OWNER TO postgres;

--
-- Name: csv_import_reports_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE csv_import_reports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.csv_import_reports_id_seq OWNER TO postgres;

--
-- Name: csv_import_reports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE csv_import_reports_id_seq OWNED BY csv_import_reports.id;


--
-- Name: currencies; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE currencies (
    id integer NOT NULL,
    name text NOT NULL
);


ALTER TABLE public.currencies OWNER TO postgres;

--
-- Name: currencies_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE currencies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.currencies_id_seq OWNER TO postgres;

--
-- Name: currencies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE currencies_id_seq OWNED BY currencies.id;


--
-- Name: custmsg; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE custmsg (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    fid integer,
    prio integer DEFAULT 3,
    msg character varying(60),
    uid integer,
    akt boolean
);


ALTER TABLE public.custmsg OWNER TO postgres;

--
-- Name: custom_variable_config_partsgroups; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE custom_variable_config_partsgroups (
    custom_variable_config_id integer NOT NULL,
    partsgroup_id integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.custom_variable_config_partsgroups OWNER TO postgres;

--
-- Name: custom_variable_configs_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE custom_variable_configs_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.custom_variable_configs_id OWNER TO postgres;

--
-- Name: custom_variable_configs; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE custom_variable_configs (
    id integer DEFAULT nextval('custom_variable_configs_id'::regclass) NOT NULL,
    name text NOT NULL,
    description text NOT NULL,
    type text NOT NULL,
    module text NOT NULL,
    default_value text,
    options text,
    searchable boolean NOT NULL,
    includeable boolean NOT NULL,
    included_by_default boolean NOT NULL,
    sortkey integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    flags text,
    CONSTRAINT custom_variable_configs_name_description_type_module_not_empty CHECK (((((type <> ''::text) AND (module <> ''::text)) AND (name <> ''::text)) AND (description <> ''::text))),
    CONSTRAINT custom_variable_configs_options_not_empty_for_select CHECK (((type <> 'select'::text) OR (COALESCE(options, ''::text) <> ''::text)))
);


ALTER TABLE public.custom_variable_configs OWNER TO postgres;

--
-- Name: custom_variables_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE custom_variables_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.custom_variables_id OWNER TO postgres;

--
-- Name: custom_variables; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE custom_variables (
    id integer DEFAULT nextval('custom_variables_id'::regclass) NOT NULL,
    config_id integer NOT NULL,
    trans_id integer NOT NULL,
    bool_value boolean,
    timestamp_value timestamp without time zone,
    text_value text,
    number_value numeric(25,5),
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    sub_module text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.custom_variables OWNER TO postgres;

--
-- Name: custom_variables_validity; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE custom_variables_validity (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    config_id integer NOT NULL,
    trans_id integer NOT NULL,
    itime timestamp without time zone DEFAULT now()
);


ALTER TABLE public.custom_variables_validity OWNER TO postgres;

--
-- Name: customer; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE customer (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    name text NOT NULL,
    department_1 text,
    department_2 text,
    street text,
    zipcode text,
    city text,
    country text,
    contact text,
    phone text,
    fax text,
    homepage text,
    email text,
    notes text,
    discount real,
    taxincluded boolean,
    creditlimit numeric(15,5) DEFAULT 0,
    terms smallint DEFAULT 0,
    customernumber text,
    cc text,
    bcc text,
    business_id integer,
    taxnumber text,
    account_number text,
    bank_code text,
    bank text,
    language text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    obsolete boolean DEFAULT false,
    username text,
    user_password text,
    salesman_id integer,
    c_vendor_id text,
    klass integer DEFAULT 0,
    language_id integer,
    payment_id integer,
    taxzone_id integer NOT NULL,
    greeting text,
    ustid text,
    iban text,
    bic text,
    direct_debit boolean DEFAULT false,
    depositor text,
    taxincluded_checked boolean,
    mandator_id text,
    mandate_date_of_signature date,
    delivery_term_id integer,
    hourly_rate numeric(8,2),
    currency_id integer NOT NULL,
    owener integer,
    employee integer,
    sw text,
    branche character varying(45),
    grafik character varying(4),
    sonder integer,
    lead integer,
    leadsrc character varying(25),
    bland integer,
    konzern integer,
    headcount integer
);


ALTER TABLE public.customer OWNER TO postgres;

--
-- Name: datev; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE datev (
    beraternr character varying(7),
    beratername character varying(9),
    mandantennr character varying(5),
    dfvkz character varying(2),
    datentraegernr character varying(3),
    abrechnungsnr character varying(6),
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    id integer NOT NULL
);


ALTER TABLE public.datev OWNER TO postgres;

--
-- Name: datev_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE datev_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.datev_id_seq OWNER TO postgres;

--
-- Name: datev_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE datev_id_seq OWNED BY datev.id;


--
-- Name: defaults; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE defaults (
    inventory_accno_id integer,
    income_accno_id integer,
    expense_accno_id integer,
    fxgain_accno_id integer,
    fxloss_accno_id integer,
    invnumber text,
    sonumber text,
    weightunit character varying(5),
    businessnumber text,
    version character varying(8),
    closedto date,
    revtrans boolean DEFAULT false,
    ponumber text,
    sqnumber text,
    rfqnumber text,
    customernumber text,
    vendornumber text,
    articlenumber text,
    servicenumber text,
    coa text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    rmanumber text,
    cnnumber text,
    accounting_method text,
    inventory_system text,
    profit_determination text,
    dunning_ar_amount_fee integer,
    dunning_ar_amount_interest integer,
    dunning_ar integer,
    pdonumber text,
    sdonumber text,
    ar_paid_accno_id integer,
    id integer NOT NULL,
    language_id integer,
    datev_check_on_sales_invoice boolean DEFAULT true,
    datev_check_on_purchase_invoice boolean DEFAULT true,
    datev_check_on_ar_transaction boolean DEFAULT true,
    datev_check_on_ap_transaction boolean DEFAULT true,
    datev_check_on_gl_transaction boolean DEFAULT true,
    payments_changeable integer DEFAULT 0 NOT NULL,
    is_changeable integer DEFAULT 2 NOT NULL,
    ir_changeable integer DEFAULT 2 NOT NULL,
    ar_changeable integer DEFAULT 2 NOT NULL,
    ap_changeable integer DEFAULT 2 NOT NULL,
    gl_changeable integer DEFAULT 2 NOT NULL,
    show_bestbefore boolean DEFAULT false,
    sales_order_show_delete boolean DEFAULT true,
    purchase_order_show_delete boolean DEFAULT true,
    sales_delivery_order_show_delete boolean DEFAULT true,
    purchase_delivery_order_show_delete boolean DEFAULT true,
    is_show_mark_as_paid boolean DEFAULT true,
    ir_show_mark_as_paid boolean DEFAULT true,
    ar_show_mark_as_paid boolean DEFAULT true,
    ap_show_mark_as_paid boolean DEFAULT true,
    warehouse_id integer,
    bin_id integer,
    company text,
    address text,
    taxnumber text,
    co_ustid text,
    duns text,
    sepa_creditor_id text,
    templates text,
    max_future_booking_interval integer DEFAULT 360,
    webdav boolean DEFAULT false,
    webdav_documents boolean DEFAULT false,
    vertreter boolean DEFAULT false,
    parts_show_image boolean DEFAULT true,
    parts_listing_image boolean DEFAULT true,
    parts_image_css text DEFAULT 'border:0;float:left;max-width:250px;margin-top:20px:margin-right:10px;margin-left:10px;'::text,
    normalize_vc_names boolean DEFAULT true,
    normalize_part_descriptions boolean DEFAULT true,
    assemblynumber text,
    show_weight boolean DEFAULT false NOT NULL,
    transfer_default boolean DEFAULT true,
    transfer_default_use_master_default_bin boolean DEFAULT false,
    transfer_default_ignore_onhand boolean DEFAULT false,
    warehouse_id_ignore_onhand integer,
    bin_id_ignore_onhand integer,
    balance_startdate_method text,
    currency_id integer NOT NULL,
    customer_hourly_rate numeric(8,2),
    signature text,
    requirement_spec_section_order_part_id integer,
    transfer_default_services boolean DEFAULT true,
    delivery_plan_show_value_of_goods boolean DEFAULT false NOT NULL,
    delivery_plan_calculate_transferred_do boolean DEFAULT false NOT NULL,
    global_bcc text DEFAULT ''::text,
    customer_projects_only_in_sales boolean DEFAULT false NOT NULL,
    reqdate_interval integer DEFAULT 0,
    require_transaction_description_ps boolean DEFAULT false NOT NULL,
    allow_sales_invoice_from_sales_quotation boolean DEFAULT true NOT NULL,
    allow_sales_invoice_from_sales_order boolean DEFAULT true NOT NULL,
    allow_new_purchase_delivery_order boolean DEFAULT true NOT NULL,
    allow_new_purchase_invoice boolean DEFAULT true NOT NULL,
    disabled_price_sources text[],
    transport_cost_reminder_article_number_id integer,
    contnumber text
);


ALTER TABLE public.defaults OWNER TO postgres;

--
-- Name: defaults_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE defaults_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.defaults_id_seq OWNER TO postgres;

--
-- Name: defaults_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE defaults_id_seq OWNED BY defaults.id;


--
-- Name: delivery_order_items_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE delivery_order_items_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.delivery_order_items_id OWNER TO postgres;

SET default_with_oids = true;

--
-- Name: delivery_order_items; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE delivery_order_items (
    id integer DEFAULT nextval('delivery_order_items_id'::regclass) NOT NULL,
    delivery_order_id integer NOT NULL,
    parts_id integer NOT NULL,
    description text,
    qty numeric(25,5),
    sellprice numeric(15,5),
    discount real,
    project_id integer,
    reqdate date,
    serialnumber text,
    ordnumber text,
    transdate text,
    cusordnumber text,
    unit character varying(20),
    base_qty real,
    longdescription text,
    lastcost numeric(15,5),
    price_factor_id integer,
    price_factor numeric(15,5) DEFAULT 1,
    marge_price_factor numeric(15,5) DEFAULT 1,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    pricegroup_id integer,
    "position" integer NOT NULL,
    active_price_source text DEFAULT ''::text NOT NULL,
    active_discount_source text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.delivery_order_items OWNER TO postgres;

SET default_with_oids = false;

--
-- Name: delivery_order_items_stock; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE delivery_order_items_stock (
    id integer DEFAULT nextval('id'::regclass) NOT NULL,
    delivery_order_item_id integer NOT NULL,
    qty numeric(15,5) NOT NULL,
    unit character varying(20) NOT NULL,
    warehouse_id integer NOT NULL,
    bin_id integer NOT NULL,
    chargenumber text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    bestbefore date
);


ALTER TABLE public.delivery_order_items_stock OWNER TO postgres;

--
-- Name: delivery_orders; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE delivery_orders (
    id integer DEFAULT nextval('id'::regclass) NOT NULL,
    donumber text NOT NULL,
    ordnumber text,
    transdate date DEFAULT now(),
    vendor_id integer,
    customer_id integer,
    reqdate date,
    shippingpoint text,
    notes text,
    intnotes text,
    employee_id integer,
    closed boolean DEFAULT false,
    delivered boolean DEFAULT false,
    cusordnumber text,
    oreqnumber text,
    department_id integer,
    shipvia text,
    cp_id integer,
    language_id integer,
    shipto_id integer,
    globalproject_id integer,
    salesman_id integer,
    transaction_description text,
    is_sales boolean,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    taxzone_id integer NOT NULL,
    taxincluded boolean,
    terms integer,
    delivery_term_id integer,
    currency_id integer NOT NULL
);


ALTER TABLE public.delivery_orders OWNER TO postgres;

--
-- Name: delivery_terms; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE delivery_terms (
    id integer DEFAULT nextval('id'::regclass) NOT NULL,
    description text,
    description_long text,
    sortkey integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.delivery_terms OWNER TO postgres;

--
-- Name: department; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE department (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    description text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.department OWNER TO postgres;

--
-- Name: docfelder; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE docfelder (
    fid integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    docid integer,
    feldname character varying(20),
    platzhalter character varying(20),
    beschreibung character varying(200),
    laenge integer,
    zeichen character varying(20),
    "position" integer
);


ALTER TABLE public.docfelder OWNER TO postgres;

--
-- Name: documents; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE documents (
    filename text,
    descript text,
    datum date,
    zeit time without time zone,
    size integer,
    pfad text,
    kunde integer,
    lock integer DEFAULT 0,
    employee integer,
    id integer DEFAULT nextval(('id'::text)::regclass)
);


ALTER TABLE public.documents OWNER TO postgres;

--
-- Name: documenttotc; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE documenttotc (
    id integer DEFAULT nextval(('crmid'::text)::regclass),
    telcall integer,
    documents integer
);


ALTER TABLE public.documenttotc OWNER TO postgres;

--
-- Name: docvorlage; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE docvorlage (
    docid integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    vorlage character varying(60),
    beschreibung character varying(255),
    file character varying(40),
    applikation character(1)
);


ALTER TABLE public.docvorlage OWNER TO postgres;

--
-- Name: drafts; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE drafts (
    id character varying(50) NOT NULL,
    module character varying(50) NOT NULL,
    submodule character varying(50) NOT NULL,
    description text,
    itime timestamp without time zone DEFAULT now(),
    form text,
    employee_id integer
);


ALTER TABLE public.drafts OWNER TO postgres;

--
-- Name: dunning; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE dunning (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    trans_id integer,
    dunning_id integer,
    dunning_level integer,
    transdate date,
    duedate date,
    fee numeric(15,5),
    interest numeric(15,5),
    dunning_config_id integer,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    fee_interest_ar_id integer
);


ALTER TABLE public.dunning OWNER TO postgres;

--
-- Name: dunning_config; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE dunning_config (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    dunning_level integer,
    dunning_description text,
    active boolean,
    auto boolean,
    email boolean,
    terms integer,
    payment_terms integer,
    fee numeric(15,5),
    interest_rate numeric(15,5),
    email_body text,
    email_subject text,
    email_attachment boolean,
    template text,
    create_invoices_for_fees boolean DEFAULT true
);


ALTER TABLE public.dunning_config OWNER TO postgres;

--
-- Name: employee; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE employee (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    login text,
    startdate date DEFAULT ('now'::text)::date,
    enddate date,
    sales boolean DEFAULT true,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    name text,
    deleted boolean DEFAULT false,
    deleted_email text,
    deleted_signature text,
    deleted_tel text,
    deleted_fax text
);


ALTER TABLE public.employee OWNER TO postgres;

--
-- Name: event_category; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE event_category (
    id integer NOT NULL,
    label text,
    color character(7),
    cat_order integer DEFAULT 1
);


ALTER TABLE public.event_category OWNER TO postgres;

--
-- Name: event_category_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE event_category_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.event_category_id_seq OWNER TO postgres;

--
-- Name: event_category_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE event_category_id_seq OWNED BY event_category.id;


--
-- Name: events; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE events (
    id integer NOT NULL,
    title text,
    duration tsrange,
    repeat character(5),
    repeat_factor smallint,
    repeat_quantity smallint,
    repeat_end timestamp without time zone,
    description text,
    location text,
    uid integer,
    prio smallint,
    category smallint,
    visibility smallint,
    "allDay" boolean,
    color character(7),
    job boolean,
    done boolean,
    job_planned_end timestamp without time zone,
    cust_vend_pers text
);


ALTER TABLE public.events OWNER TO postgres;

--
-- Name: events_tmp_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE events_tmp_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.events_tmp_id_seq OWNER TO postgres;

--
-- Name: events_tmp_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE events_tmp_id_seq OWNED BY events.id;


--
-- Name: exchangerate; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE exchangerate (
    transdate date,
    buy numeric(15,5),
    sell numeric(15,5),
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    id integer NOT NULL,
    currency_id integer NOT NULL
);


ALTER TABLE public.exchangerate OWNER TO postgres;

--
-- Name: exchangerate_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE exchangerate_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.exchangerate_id_seq OWNER TO postgres;

--
-- Name: exchangerate_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE exchangerate_id_seq OWNED BY exchangerate.id;


--
-- Name: extra_felder; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE extra_felder (
    id integer DEFAULT nextval(('extraid'::text)::regclass) NOT NULL,
    owner integer,
    tab character(1),
    fkey text,
    fval text
);


ALTER TABLE public.extra_felder OWNER TO postgres;

--
-- Name: extraid; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE extraid
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 2147483647
    CACHE 1;


ALTER TABLE public.extraid OWNER TO postgres;

--
-- Name: finanzamt; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE finanzamt (
    fa_land_nr text,
    fa_bufa_nr text,
    fa_name text,
    fa_strasse text,
    fa_plz text,
    fa_ort text,
    fa_telefon text,
    fa_fax text,
    fa_plz_grosskunden text,
    fa_plz_postfach text,
    fa_postfach text,
    fa_blz_1 text,
    fa_kontonummer_1 text,
    fa_bankbezeichnung_1 text,
    fa_blz_2 text,
    fa_kontonummer_2 text,
    fa_bankbezeichnung_2 text,
    fa_oeffnungszeiten text,
    fa_email text,
    fa_internet text,
    id integer NOT NULL
);


ALTER TABLE public.finanzamt OWNER TO postgres;

--
-- Name: finanzamt_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE finanzamt_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.finanzamt_id_seq OWNER TO postgres;

--
-- Name: finanzamt_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE finanzamt_id_seq OWNED BY finanzamt.id;


--
-- Name: follow_up_access; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE follow_up_access (
    who integer NOT NULL,
    what integer NOT NULL,
    id integer NOT NULL
);


ALTER TABLE public.follow_up_access OWNER TO postgres;

--
-- Name: follow_up_access_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE follow_up_access_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.follow_up_access_id_seq OWNER TO postgres;

--
-- Name: follow_up_access_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE follow_up_access_id_seq OWNED BY follow_up_access.id;


--
-- Name: follow_up_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE follow_up_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.follow_up_id OWNER TO postgres;

--
-- Name: follow_up_link_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE follow_up_link_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.follow_up_link_id OWNER TO postgres;

--
-- Name: follow_up_links; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE follow_up_links (
    id integer DEFAULT nextval('follow_up_link_id'::regclass) NOT NULL,
    follow_up_id integer NOT NULL,
    trans_id integer NOT NULL,
    trans_type text NOT NULL,
    trans_info text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.follow_up_links OWNER TO postgres;

--
-- Name: follow_ups; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE follow_ups (
    id integer DEFAULT nextval('follow_up_id'::regclass) NOT NULL,
    follow_up_date date NOT NULL,
    created_for_user integer NOT NULL,
    done boolean DEFAULT false,
    note_id integer NOT NULL,
    created_by integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.follow_ups OWNER TO postgres;

--
-- Name: generic_translations; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE generic_translations (
    id integer NOT NULL,
    language_id integer,
    translation_type character varying(100) NOT NULL,
    translation_id integer,
    translation text
);


ALTER TABLE public.generic_translations OWNER TO postgres;

--
-- Name: generic_translations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE generic_translations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.generic_translations_id_seq OWNER TO postgres;

--
-- Name: generic_translations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE generic_translations_id_seq OWNED BY generic_translations.id;


--
-- Name: gl; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gl (
    id integer DEFAULT nextval(('glid'::text)::regclass) NOT NULL,
    reference text,
    description text,
    transdate date DEFAULT ('now'::text)::date,
    gldate date DEFAULT ('now'::text)::date,
    employee_id integer,
    notes text,
    department_id integer,
    taxincluded boolean,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    type text,
    ob_transaction boolean,
    cb_transaction boolean,
    storno boolean DEFAULT false,
    storno_id integer
);


ALTER TABLE public.gl OWNER TO postgres;

--
-- Name: glid; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE glid
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 2147483647
    CACHE 1;


ALTER TABLE public.glid OWNER TO postgres;

--
-- Name: grpusr; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE grpusr (
    gid integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    grpid integer,
    usrid integer
);


ALTER TABLE public.grpusr OWNER TO postgres;

--
-- Name: gruppenname; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE gruppenname (
    grpid integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    grpname character varying(40),
    rechte character(1) DEFAULT 'w'::bpchar
);


ALTER TABLE public.gruppenname OWNER TO postgres;

--
-- Name: history; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE history (
    mid integer,
    itime timestamp without time zone DEFAULT now(),
    art character varying(20),
    bezug integer,
    beschreibung text
);


ALTER TABLE public.history OWNER TO postgres;

--
-- Name: history_erp; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE history_erp (
    id integer DEFAULT nextval('id'::regclass) NOT NULL,
    trans_id integer,
    employee_id integer,
    addition text,
    what_done text,
    itime timestamp without time zone DEFAULT now(),
    snumbers text
);


ALTER TABLE public.history_erp OWNER TO postgres;

--
-- Name: inventory; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE inventory (
    warehouse_id integer NOT NULL,
    parts_id integer NOT NULL,
    oe_id integer,
    delivery_order_items_stock_id integer,
    shippingdate date,
    employee_id integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    bin_id integer NOT NULL,
    qty numeric(25,5),
    trans_id integer NOT NULL,
    trans_type_id integer NOT NULL,
    project_id integer,
    chargenumber text DEFAULT ''::text NOT NULL,
    comment text,
    bestbefore date,
    id integer NOT NULL
);


ALTER TABLE public.inventory OWNER TO postgres;

--
-- Name: inventory_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE inventory_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.inventory_id_seq OWNER TO postgres;

--
-- Name: inventory_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE inventory_id_seq OWNED BY inventory.id;


SET default_with_oids = true;

--
-- Name: invoice; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE invoice (
    id integer DEFAULT nextval(('invoiceid'::text)::regclass) NOT NULL,
    trans_id integer,
    parts_id integer,
    description text,
    qty real,
    allocated real,
    sellprice numeric(15,5),
    fxsellprice numeric(15,5),
    discount real,
    assemblyitem boolean DEFAULT false,
    project_id integer,
    deliverydate date,
    serialnumber text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    pricegroup_id integer,
    ordnumber text,
    transdate text,
    cusordnumber text,
    unit character varying(20),
    base_qty real,
    subtotal boolean DEFAULT false,
    longdescription text,
    marge_total numeric(15,5),
    marge_percent numeric(15,5),
    lastcost numeric(15,5),
    price_factor_id integer,
    price_factor numeric(15,5) DEFAULT 1,
    marge_price_factor numeric(15,5) DEFAULT 1,
    donumber text,
    "position" integer NOT NULL,
    active_price_source text DEFAULT ''::text NOT NULL,
    active_discount_source text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.invoice OWNER TO postgres;

--
-- Name: invoiceid; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE invoiceid
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 2147483647
    CACHE 1;


ALTER TABLE public.invoiceid OWNER TO postgres;

SET default_with_oids = false;

--
-- Name: labels; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE labels (
    id integer DEFAULT nextval(('crmid'::text)::regclass),
    name character varying(32),
    cust character(1),
    papersize character varying(10),
    metric character(2),
    marginleft double precision,
    margintop double precision,
    nx integer,
    ny integer,
    spacex double precision,
    spacey double precision,
    width double precision,
    height double precision,
    fontsize integer,
    employee integer
);


ALTER TABLE public.labels OWNER TO postgres;

--
-- Name: labeltxt; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE labeltxt (
    id integer DEFAULT nextval(('crmid'::text)::regclass),
    lid integer,
    font integer,
    zeile text
);


ALTER TABLE public.labeltxt OWNER TO postgres;

--
-- Name: language; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE language (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    description text,
    template_code text,
    article_code text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    output_numberformat text,
    output_dateformat text,
    output_longdates boolean
);


ALTER TABLE public.language OWNER TO postgres;

--
-- Name: leads; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE leads (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    lead character varying(50)
);


ALTER TABLE public.leads OWNER TO postgres;

--
-- Name: mailvorlage; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE mailvorlage (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    cause character varying(120),
    c_long text,
    employee integer
);


ALTER TABLE public.mailvorlage OWNER TO postgres;

--
-- Name: makemodel_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE makemodel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.makemodel_id_seq OWNER TO postgres;

--
-- Name: makemodel; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE makemodel (
    parts_id integer,
    model text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    lastcost numeric(15,5),
    lastupdate date,
    sortorder integer,
    make integer,
    id integer DEFAULT nextval('makemodel_id_seq'::regclass) NOT NULL
);


ALTER TABLE public.makemodel OWNER TO postgres;

--
-- Name: maschine; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE maschine (
    id integer DEFAULT nextval(('crmid'::text)::regclass),
    parts_id integer,
    serialnumber text,
    standort text,
    inspdatum date,
    counter bigint
);


ALTER TABLE public.maschine OWNER TO postgres;

--
-- Name: maschmat; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE maschmat (
    mid integer,
    aid integer,
    parts_id integer,
    betrag numeric(15,5),
    menge numeric(10,3)
);


ALTER TABLE public.maschmat OWNER TO postgres;

--
-- Name: note_id; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE note_id
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.note_id OWNER TO postgres;

--
-- Name: notes; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE notes (
    id integer DEFAULT nextval('note_id'::regclass) NOT NULL,
    subject text,
    body text,
    created_by integer NOT NULL,
    trans_id integer,
    trans_module character varying(10),
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.notes OWNER TO postgres;

--
-- Name: oe; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE oe (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    ordnumber text NOT NULL,
    transdate date DEFAULT ('now'::text)::date,
    vendor_id integer,
    customer_id integer,
    amount numeric(15,5),
    netamount numeric(15,5),
    reqdate date,
    taxincluded boolean,
    shippingpoint text,
    notes text,
    employee_id integer,
    closed boolean DEFAULT false,
    quotation boolean DEFAULT false,
    quonumber text,
    cusordnumber text,
    intnotes text,
    department_id integer,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    shipvia text,
    cp_id integer,
    language_id integer,
    payment_id integer,
    delivery_customer_id integer,
    delivery_vendor_id integer,
    taxzone_id integer NOT NULL,
    proforma boolean DEFAULT false,
    shipto_id integer,
    order_probability integer DEFAULT 0 NOT NULL,
    expected_billing_date date,
    globalproject_id integer,
    delivered boolean DEFAULT false,
    salesman_id integer,
    marge_total numeric(15,5),
    marge_percent numeric(15,5),
    transaction_description text,
    delivery_term_id integer,
    currency_id integer NOT NULL
);


ALTER TABLE public.oe OWNER TO postgres;

--
-- Name: opport_status; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE opport_status (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    statusname character varying(50),
    sort integer
);


ALTER TABLE public.opport_status OWNER TO postgres;

--
-- Name: opportunity; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE opportunity (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    oppid integer DEFAULT 0 NOT NULL,
    fid integer,
    tab character(1),
    title character varying(100),
    betrag numeric(15,5),
    zieldatum date,
    chance integer,
    status integer,
    salesman integer,
    next character varying(100),
    notiz text,
    auftrag integer DEFAULT 0,
    itime timestamp without time zone DEFAULT now(),
    iemployee integer,
    memployee integer
);


ALTER TABLE public.opportunity OWNER TO postgres;

SET default_with_oids = true;

--
-- Name: orderitems; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE orderitems (
    trans_id integer,
    parts_id integer,
    description text,
    qty real,
    sellprice numeric(15,5),
    discount real,
    project_id integer,
    reqdate date,
    ship real,
    serialnumber text,
    id integer DEFAULT nextval(('orderitemsid'::text)::regclass) NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    pricegroup_id integer,
    ordnumber text,
    transdate text,
    cusordnumber text,
    unit character varying(20),
    base_qty real,
    subtotal boolean DEFAULT false,
    longdescription text,
    marge_total numeric(15,5),
    marge_percent numeric(15,5),
    lastcost numeric(15,5),
    price_factor_id integer,
    price_factor numeric(15,5) DEFAULT 1,
    marge_price_factor numeric(15,5) DEFAULT 1,
    "position" integer NOT NULL,
    active_price_source text DEFAULT ''::text NOT NULL,
    active_discount_source text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.orderitems OWNER TO postgres;

--
-- Name: orderitemsid; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE orderitemsid
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 2147483647
    CACHE 1
    CYCLE;


ALTER TABLE public.orderitemsid OWNER TO postgres;

--
-- Name: parts; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE parts (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    partnumber text NOT NULL,
    description text,
    listprice numeric(15,5),
    sellprice numeric(15,5),
    lastcost numeric(15,5),
    priceupdate date DEFAULT ('now'::text)::date,
    weight real,
    notes text,
    makemodel boolean DEFAULT false,
    assembly boolean DEFAULT false,
    alternate boolean DEFAULT false,
    rop real,
    inventory_accno_id integer,
    income_accno_id integer,
    expense_accno_id integer,
    shop boolean DEFAULT false,
    obsolete boolean DEFAULT false,
    bom boolean DEFAULT false,
    image text,
    drawing text,
    microfiche text,
    partsgroup_id integer,
    ve integer,
    gv numeric(15,5),
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    unit character varying(20) NOT NULL,
    formel text,
    not_discountable boolean DEFAULT false,
    buchungsgruppen_id integer,
    payment_id integer,
    ean text,
    price_factor_id integer,
    onhand numeric(25,5) DEFAULT 0,
    stockable boolean DEFAULT false,
    has_sernumber boolean DEFAULT false,
    warehouse_id integer,
    bin_id integer
);


ALTER TABLE public.parts OWNER TO postgres;

--
-- Name: partsgroup; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE partsgroup (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    partsgroup text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.partsgroup OWNER TO postgres;

SET default_with_oids = false;

--
-- Name: payment_terms; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE payment_terms (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    description text,
    description_long text,
    terms_netto integer,
    terms_skonto integer,
    percent_skonto real,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    ranking integer,
    sortkey integer NOT NULL
);


ALTER TABLE public.payment_terms OWNER TO postgres;

--
-- Name: periodic_invoices; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE periodic_invoices (
    id integer DEFAULT nextval('id'::regclass) NOT NULL,
    config_id integer NOT NULL,
    ar_id integer NOT NULL,
    period_start_date date NOT NULL,
    itime timestamp without time zone DEFAULT now()
);


ALTER TABLE public.periodic_invoices OWNER TO postgres;

--
-- Name: periodic_invoices_configs; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE periodic_invoices_configs (
    id integer DEFAULT nextval('id'::regclass) NOT NULL,
    oe_id integer NOT NULL,
    periodicity character varying(10) NOT NULL,
    print boolean DEFAULT false,
    printer_id integer,
    copies integer,
    active boolean DEFAULT true,
    terminated boolean DEFAULT false,
    start_date date,
    end_date date,
    ar_chart_id integer NOT NULL,
    extend_automatically_by integer,
    first_billing_date date
);


ALTER TABLE public.periodic_invoices_configs OWNER TO postgres;

--
-- Name: postit; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE postit (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    cause character varying(100),
    notes text,
    employee integer,
    date timestamp without time zone NOT NULL
);


ALTER TABLE public.postit OWNER TO postgres;

--
-- Name: price_factors; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE price_factors (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    description text,
    factor numeric(15,5),
    sortkey integer
);


ALTER TABLE public.price_factors OWNER TO postgres;

--
-- Name: price_rule_items; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE price_rule_items (
    id integer NOT NULL,
    price_rules_id integer NOT NULL,
    type text,
    op text,
    custom_variable_configs_id integer,
    value_text text,
    value_int integer,
    value_date date,
    value_num numeric(15,5),
    itime timestamp without time zone,
    mtime timestamp without time zone
);


ALTER TABLE public.price_rule_items OWNER TO postgres;

--
-- Name: price_rule_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE price_rule_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.price_rule_items_id_seq OWNER TO postgres;

--
-- Name: price_rule_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE price_rule_items_id_seq OWNED BY price_rule_items.id;


--
-- Name: price_rules; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE price_rules (
    id integer NOT NULL,
    name text,
    type text,
    priority integer DEFAULT 3 NOT NULL,
    price numeric(15,5),
    reduction numeric(15,5),
    obsolete boolean DEFAULT false NOT NULL,
    itime timestamp without time zone,
    mtime timestamp without time zone,
    discount numeric(15,5)
);


ALTER TABLE public.price_rules OWNER TO postgres;

--
-- Name: price_rules_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE price_rules_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.price_rules_id_seq OWNER TO postgres;

--
-- Name: price_rules_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE price_rules_id_seq OWNED BY price_rules.id;


--
-- Name: pricegroup; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE pricegroup (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    pricegroup text NOT NULL
);


ALTER TABLE public.pricegroup OWNER TO postgres;

--
-- Name: prices; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE prices (
    parts_id integer,
    pricegroup_id integer,
    price numeric(15,5),
    id integer NOT NULL
);


ALTER TABLE public.prices OWNER TO postgres;

--
-- Name: prices_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE prices_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.prices_id_seq OWNER TO postgres;

--
-- Name: prices_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE prices_id_seq OWNED BY prices.id;


--
-- Name: printers; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE printers (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    printer_description text NOT NULL,
    printer_command text,
    template_code text
);


ALTER TABLE public.printers OWNER TO postgres;

--
-- Name: project; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE project (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    projectnumber text,
    description text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    active boolean DEFAULT true,
    customer_id integer,
    valid boolean DEFAULT true,
    project_type_id integer NOT NULL,
    start_date date,
    end_date date,
    billable_customer_id integer,
    budget_cost numeric(15,5) DEFAULT 0 NOT NULL,
    order_value numeric(15,5) DEFAULT 0 NOT NULL,
    budget_minutes integer DEFAULT 0 NOT NULL,
    timeframe boolean DEFAULT false NOT NULL,
    project_status_id integer NOT NULL
);


ALTER TABLE public.project OWNER TO postgres;

--
-- Name: project_participants; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE project_participants (
    id integer NOT NULL,
    project_id integer NOT NULL,
    employee_id integer NOT NULL,
    project_role_id integer NOT NULL,
    minutes integer DEFAULT 0 NOT NULL,
    cost_per_hour numeric(15,5),
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.project_participants OWNER TO postgres;

--
-- Name: project_participants_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE project_participants_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_participants_id_seq OWNER TO postgres;

--
-- Name: project_participants_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE project_participants_id_seq OWNED BY project_participants.id;


--
-- Name: project_phase_participants; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE project_phase_participants (
    id integer NOT NULL,
    project_phase_id integer NOT NULL,
    employee_id integer NOT NULL,
    project_role_id integer NOT NULL,
    minutes integer DEFAULT 0 NOT NULL,
    cost_per_hour numeric(15,5),
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.project_phase_participants OWNER TO postgres;

--
-- Name: project_phase_participants_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE project_phase_participants_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_phase_participants_id_seq OWNER TO postgres;

--
-- Name: project_phase_participants_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE project_phase_participants_id_seq OWNED BY project_phase_participants.id;


--
-- Name: project_phases; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE project_phases (
    id integer NOT NULL,
    project_id integer,
    start_date date,
    end_date date,
    name text NOT NULL,
    description text NOT NULL,
    budget_minutes integer DEFAULT 0 NOT NULL,
    budget_cost numeric(15,5) DEFAULT 0 NOT NULL,
    general_minutes integer DEFAULT 0 NOT NULL,
    general_cost_per_hour numeric(15,5) DEFAULT 0 NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.project_phases OWNER TO postgres;

--
-- Name: project_phases_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE project_phases_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_phases_id_seq OWNER TO postgres;

--
-- Name: project_phases_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE project_phases_id_seq OWNED BY project_phases.id;


--
-- Name: project_roles; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE project_roles (
    id integer NOT NULL,
    name text NOT NULL,
    description text NOT NULL,
    "position" integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.project_roles OWNER TO postgres;

--
-- Name: project_roles_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE project_roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_roles_id_seq OWNER TO postgres;

--
-- Name: project_roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE project_roles_id_seq OWNED BY project_roles.id;


--
-- Name: project_statuses; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE project_statuses (
    id integer NOT NULL,
    name text NOT NULL,
    description text NOT NULL,
    "position" integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.project_statuses OWNER TO postgres;

--
-- Name: project_status_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE project_status_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_status_id_seq OWNER TO postgres;

--
-- Name: project_status_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE project_status_id_seq OWNED BY project_statuses.id;


--
-- Name: project_types; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE project_types (
    id integer NOT NULL,
    "position" integer NOT NULL,
    description text,
    internal boolean DEFAULT false NOT NULL
);


ALTER TABLE public.project_types OWNER TO postgres;

--
-- Name: project_types_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE project_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.project_types_id_seq OWNER TO postgres;

--
-- Name: project_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE project_types_id_seq OWNED BY project_types.id;


--
-- Name: record_links; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE record_links (
    from_table character varying(50) NOT NULL,
    from_id integer NOT NULL,
    to_table character varying(50) NOT NULL,
    to_id integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    id integer NOT NULL
);


ALTER TABLE public.record_links OWNER TO postgres;

--
-- Name: record_links_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE record_links_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.record_links_id_seq OWNER TO postgres;

--
-- Name: record_links_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE record_links_id_seq OWNED BY record_links.id;


--
-- Name: repauftrag; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE repauftrag (
    aid integer,
    mid integer,
    cause text,
    schaden text,
    reparatur text,
    bearbdate timestamp without time zone,
    employee integer,
    bearbeiter integer,
    anlagedatum timestamp without time zone,
    status integer,
    kdnr integer,
    counter bigint
);


ALTER TABLE public.repauftrag OWNER TO postgres;

--
-- Name: requirement_spec_acceptance_statuses; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_acceptance_statuses (
    id integer NOT NULL,
    name text NOT NULL,
    description text,
    "position" integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.requirement_spec_acceptance_statuses OWNER TO postgres;

--
-- Name: requirement_spec_acceptance_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_acceptance_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_acceptance_statuses_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_acceptance_statuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_acceptance_statuses_id_seq OWNED BY requirement_spec_acceptance_statuses.id;


--
-- Name: requirement_spec_complexities; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_complexities (
    id integer NOT NULL,
    description text NOT NULL,
    "position" integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.requirement_spec_complexities OWNER TO postgres;

--
-- Name: requirement_spec_complexities_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_complexities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_complexities_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_complexities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_complexities_id_seq OWNED BY requirement_spec_complexities.id;


--
-- Name: requirement_spec_item_dependencies; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_item_dependencies (
    depending_item_id integer NOT NULL,
    depended_item_id integer NOT NULL
);


ALTER TABLE public.requirement_spec_item_dependencies OWNER TO postgres;

--
-- Name: requirement_spec_items; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_items (
    id integer NOT NULL,
    requirement_spec_id integer NOT NULL,
    item_type text NOT NULL,
    parent_id integer,
    "position" integer NOT NULL,
    fb_number text NOT NULL,
    title text,
    description text,
    complexity_id integer,
    risk_id integer,
    time_estimation numeric(12,2) DEFAULT 0 NOT NULL,
    is_flagged boolean DEFAULT false NOT NULL,
    acceptance_status_id integer,
    acceptance_text text,
    itime timestamp without time zone DEFAULT now() NOT NULL,
    mtime timestamp without time zone,
    order_part_id integer,
    CONSTRAINT valid_item_type CHECK ((((item_type = 'section'::text) OR (item_type = 'function-block'::text)) OR (item_type = 'sub-function-block'::text))),
    CONSTRAINT valid_parent_id_for_item_type CHECK (
CASE
    WHEN (item_type = 'section'::text) THEN (parent_id IS NULL)
    ELSE (parent_id IS NOT NULL)
END)
);


ALTER TABLE public.requirement_spec_items OWNER TO postgres;

--
-- Name: requirement_spec_items_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_items_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_items_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_items_id_seq OWNED BY requirement_spec_items.id;


--
-- Name: requirement_spec_orders; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_orders (
    id integer NOT NULL,
    requirement_spec_id integer NOT NULL,
    order_id integer NOT NULL,
    version_id integer,
    itime timestamp without time zone DEFAULT now() NOT NULL,
    mtime timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.requirement_spec_orders OWNER TO postgres;

--
-- Name: requirement_spec_orders_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_orders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_orders_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_orders_id_seq OWNED BY requirement_spec_orders.id;


--
-- Name: requirement_spec_parts; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_parts (
    id integer NOT NULL,
    requirement_spec_id integer NOT NULL,
    part_id integer NOT NULL,
    unit_id integer NOT NULL,
    qty numeric(15,5) NOT NULL,
    description text NOT NULL,
    "position" integer NOT NULL
);


ALTER TABLE public.requirement_spec_parts OWNER TO postgres;

--
-- Name: requirement_spec_parts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_parts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_parts_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_parts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_parts_id_seq OWNED BY requirement_spec_parts.id;


--
-- Name: requirement_spec_pictures; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_pictures (
    id integer NOT NULL,
    requirement_spec_id integer NOT NULL,
    text_block_id integer NOT NULL,
    "position" integer NOT NULL,
    number text NOT NULL,
    description text,
    picture_file_name text NOT NULL,
    picture_content_type text NOT NULL,
    picture_mtime timestamp without time zone DEFAULT now() NOT NULL,
    picture_content bytea NOT NULL,
    picture_width integer NOT NULL,
    picture_height integer NOT NULL,
    thumbnail_content_type text NOT NULL,
    thumbnail_content bytea NOT NULL,
    thumbnail_width integer NOT NULL,
    thumbnail_height integer NOT NULL,
    itime timestamp without time zone DEFAULT now() NOT NULL,
    mtime timestamp without time zone
);


ALTER TABLE public.requirement_spec_pictures OWNER TO postgres;

--
-- Name: requirement_spec_pictures_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_pictures_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_pictures_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_pictures_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_pictures_id_seq OWNED BY requirement_spec_pictures.id;


--
-- Name: requirement_spec_predefined_texts; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_predefined_texts (
    id integer NOT NULL,
    description text NOT NULL,
    title text NOT NULL,
    text text NOT NULL,
    "position" integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    useable_for_text_blocks boolean DEFAULT false NOT NULL,
    useable_for_sections boolean DEFAULT false NOT NULL
);


ALTER TABLE public.requirement_spec_predefined_texts OWNER TO postgres;

--
-- Name: requirement_spec_predefined_texts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_predefined_texts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_predefined_texts_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_predefined_texts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_predefined_texts_id_seq OWNED BY requirement_spec_predefined_texts.id;


--
-- Name: requirement_spec_risks; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_risks (
    id integer NOT NULL,
    description text NOT NULL,
    "position" integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.requirement_spec_risks OWNER TO postgres;

--
-- Name: requirement_spec_risks_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_risks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_risks_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_risks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_risks_id_seq OWNED BY requirement_spec_risks.id;


--
-- Name: requirement_spec_statuses; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_statuses (
    id integer NOT NULL,
    name text NOT NULL,
    description text NOT NULL,
    "position" integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.requirement_spec_statuses OWNER TO postgres;

--
-- Name: requirement_spec_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_statuses_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_statuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_statuses_id_seq OWNED BY requirement_spec_statuses.id;


--
-- Name: requirement_spec_text_blocks; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_text_blocks (
    id integer NOT NULL,
    requirement_spec_id integer NOT NULL,
    title text NOT NULL,
    text text,
    "position" integer NOT NULL,
    output_position integer DEFAULT 1 NOT NULL,
    is_flagged boolean DEFAULT false NOT NULL,
    itime timestamp without time zone DEFAULT now() NOT NULL,
    mtime timestamp without time zone
);


ALTER TABLE public.requirement_spec_text_blocks OWNER TO postgres;

--
-- Name: requirement_spec_text_blocks_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_text_blocks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_text_blocks_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_text_blocks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_text_blocks_id_seq OWNED BY requirement_spec_text_blocks.id;


--
-- Name: requirement_spec_types; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_types (
    id integer NOT NULL,
    description text NOT NULL,
    "position" integer NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    section_number_format text DEFAULT 'A00'::text NOT NULL,
    function_block_number_format text DEFAULT 'FB000'::text NOT NULL,
    template_file_name text
);


ALTER TABLE public.requirement_spec_types OWNER TO postgres;

--
-- Name: requirement_spec_types_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_types_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_types_id_seq OWNED BY requirement_spec_types.id;


--
-- Name: requirement_spec_versions; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_spec_versions (
    id integer NOT NULL,
    version_number integer,
    description text NOT NULL,
    comment text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    requirement_spec_id integer NOT NULL,
    working_copy_id integer
);


ALTER TABLE public.requirement_spec_versions OWNER TO postgres;

--
-- Name: requirement_spec_versions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_spec_versions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_spec_versions_id_seq OWNER TO postgres;

--
-- Name: requirement_spec_versions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_spec_versions_id_seq OWNED BY requirement_spec_versions.id;


--
-- Name: requirement_specs; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE requirement_specs (
    id integer NOT NULL,
    type_id integer NOT NULL,
    status_id integer,
    customer_id integer,
    project_id integer,
    title text NOT NULL,
    hourly_rate numeric(8,2) DEFAULT 0 NOT NULL,
    working_copy_id integer,
    previous_section_number integer NOT NULL,
    previous_fb_number integer NOT NULL,
    is_template boolean DEFAULT false,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    time_estimation numeric(12,2) DEFAULT 0 NOT NULL,
    previous_picture_number integer DEFAULT 0 NOT NULL,
    CONSTRAINT requirement_specs_is_template_or_has_customer_status_type CHECK ((is_template OR (((type_id IS NOT NULL) AND (status_id IS NOT NULL)) AND (customer_id IS NOT NULL))))
);


ALTER TABLE public.requirement_specs OWNER TO postgres;

--
-- Name: requirement_specs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE requirement_specs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.requirement_specs_id_seq OWNER TO postgres;

--
-- Name: requirement_specs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE requirement_specs_id_seq OWNED BY requirement_specs.id;


--
-- Name: schema_info; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE schema_info (
    tag text NOT NULL,
    login text,
    itime timestamp without time zone DEFAULT now()
);


ALTER TABLE public.schema_info OWNER TO postgres;

--
-- Name: sepa_export_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE sepa_export_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.sepa_export_id_seq OWNER TO postgres;

--
-- Name: sepa_export; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE sepa_export (
    id integer DEFAULT nextval('sepa_export_id_seq'::regclass) NOT NULL,
    employee_id integer NOT NULL,
    executed boolean DEFAULT false,
    closed boolean DEFAULT false,
    itime timestamp without time zone DEFAULT now(),
    vc character varying(10)
);


ALTER TABLE public.sepa_export OWNER TO postgres;

--
-- Name: sepa_export_items; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE sepa_export_items (
    id integer DEFAULT nextval('id'::regclass) NOT NULL,
    sepa_export_id integer NOT NULL,
    ap_id integer,
    chart_id integer NOT NULL,
    amount numeric(25,5),
    reference character varying(35),
    requested_execution_date date,
    executed boolean DEFAULT false,
    execution_date date,
    our_iban character varying(100),
    our_bic character varying(100),
    vc_iban character varying(100),
    vc_bic character varying(100),
    end_to_end_id character varying(35),
    our_depositor text,
    vc_depositor text,
    ar_id integer,
    vc_mandator_id text,
    vc_mandate_date_of_signature date
);


ALTER TABLE public.sepa_export_items OWNER TO postgres;

--
-- Name: shipto; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE shipto (
    trans_id integer,
    shiptoname text,
    shiptodepartment_1 text,
    shiptodepartment_2 text,
    shiptostreet text,
    shiptozipcode text,
    shiptocity text,
    shiptocountry text,
    shiptocontact text,
    shiptophone text,
    shiptofax text,
    shiptoemail text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    module text,
    shipto_id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    shiptocp_gender text,
    shiptoowener integer,
    shiptoemployee integer,
    shiptobland integer
);


ALTER TABLE public.shipto OWNER TO postgres;

--
-- Name: status; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE status (
    trans_id integer,
    formname text,
    printed boolean DEFAULT false,
    emailed boolean DEFAULT false,
    spoolfile text,
    chart_id integer,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    id integer NOT NULL
);


ALTER TABLE public.status OWNER TO postgres;

--
-- Name: status_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE status_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.status_id_seq OWNER TO postgres;

--
-- Name: status_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE status_id_seq OWNED BY status.id;


--
-- Name: tax; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE tax (
    chart_id integer,
    rate numeric(15,5) DEFAULT 0 NOT NULL,
    taxnumber text,
    taxkey integer NOT NULL,
    taxdescription text NOT NULL,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    chart_categories text NOT NULL
);


ALTER TABLE public.tax OWNER TO postgres;

--
-- Name: tax_zones; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE tax_zones (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    description text,
    sortkey integer NOT NULL,
    obsolete boolean DEFAULT false
);


ALTER TABLE public.tax_zones OWNER TO postgres;

--
-- Name: taxkeys; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE taxkeys (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    chart_id integer NOT NULL,
    tax_id integer NOT NULL,
    taxkey_id integer NOT NULL,
    pos_ustva integer,
    startdate date NOT NULL
);


ALTER TABLE public.taxkeys OWNER TO postgres;

--
-- Name: taxzone_charts; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE taxzone_charts (
    id integer NOT NULL,
    taxzone_id integer NOT NULL,
    buchungsgruppen_id integer NOT NULL,
    income_accno_id integer NOT NULL,
    expense_accno_id integer NOT NULL,
    itime timestamp without time zone DEFAULT now()
);


ALTER TABLE public.taxzone_charts OWNER TO postgres;

--
-- Name: taxzone_charts_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE taxzone_charts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.taxzone_charts_id_seq OWNER TO postgres;

--
-- Name: taxzone_charts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE taxzone_charts_id_seq OWNED BY taxzone_charts.id;


--
-- Name: telcall; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE telcall (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    termin_id integer,
    cause text,
    caller_id integer NOT NULL,
    calldate timestamp without time zone NOT NULL,
    c_long text,
    employee integer,
    kontakt character(1),
    "inout" character(1) DEFAULT 'i'::bpchar,
    bezug integer,
    dokument integer
);


ALTER TABLE public.telcall OWNER TO postgres;

--
-- Name: telcallhistory; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE telcallhistory (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    orgid integer,
    cause text,
    caller_id integer NOT NULL,
    calldate timestamp without time zone NOT NULL,
    c_long text,
    employee integer,
    kontakt character(1),
    bezug integer,
    dokument integer,
    chgid integer,
    grund character(1),
    datum timestamp without time zone NOT NULL
);


ALTER TABLE public.telcallhistory OWNER TO postgres;

--
-- Name: telnr; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE telnr (
    id integer,
    tabelle character(1),
    nummer character varying(20)
);


ALTER TABLE public.telnr OWNER TO postgres;

--
-- Name: tempcsvdata; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE tempcsvdata (
    uid integer,
    csvdaten text,
    id integer
);


ALTER TABLE public.tempcsvdata OWNER TO postgres;

--
-- Name: termdate; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE termdate (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    termid integer,
    datum integer,
    jahr integer,
    kw integer,
    tag character(2),
    monat character(2),
    idx integer
);


ALTER TABLE public.termdate OWNER TO postgres;

--
-- Name: termincat; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE termincat (
    catid integer NOT NULL,
    catname text,
    sorder integer,
    ccolor character(6)
);


ALTER TABLE public.termincat OWNER TO postgres;

--
-- Name: termine; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE termine (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    cause character varying(45),
    c_cause text,
    start timestamp without time zone,
    stop timestamp without time zone,
    repeat integer,
    ft character(1),
    starttag date,
    stoptag date,
    startzeit character(5),
    stopzeit character(5),
    privat boolean DEFAULT false,
    uid integer,
    kategorie integer DEFAULT 0,
    location text,
    syncid text
);


ALTER TABLE public.termine OWNER TO postgres;

--
-- Name: terminmember; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE terminmember (
    termin integer,
    member integer,
    tabelle character(1)
);


ALTER TABLE public.terminmember OWNER TO postgres;

--
-- Name: timetrack; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE timetrack (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    fid integer,
    tab character(1),
    ttname text NOT NULL,
    budget numeric(15,5),
    ttdescription text,
    startdate date,
    stopdate date,
    aim integer,
    active boolean DEFAULT true,
    uid integer NOT NULL
);


ALTER TABLE public.timetrack OWNER TO postgres;

--
-- Name: todo_user_config; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE todo_user_config (
    employee_id integer NOT NULL,
    show_after_login boolean DEFAULT true,
    show_follow_ups boolean DEFAULT true,
    show_follow_ups_login boolean DEFAULT true,
    show_overdue_sales_quotations boolean DEFAULT true,
    show_overdue_sales_quotations_login boolean DEFAULT true,
    id integer NOT NULL
);


ALTER TABLE public.todo_user_config OWNER TO postgres;

--
-- Name: todo_user_config_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE todo_user_config_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.todo_user_config_id_seq OWNER TO postgres;

--
-- Name: todo_user_config_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE todo_user_config_id_seq OWNED BY todo_user_config.id;


--
-- Name: transfer_type; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE transfer_type (
    id integer DEFAULT nextval('id'::regclass) NOT NULL,
    direction character varying(10) NOT NULL,
    description text,
    sortkey integer,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone
);


ALTER TABLE public.transfer_type OWNER TO postgres;

--
-- Name: translation; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE translation (
    parts_id integer,
    language_id integer,
    translation text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    longdescription text,
    id integer NOT NULL
);


ALTER TABLE public.translation OWNER TO postgres;

--
-- Name: translation_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE translation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.translation_id_seq OWNER TO postgres;

--
-- Name: translation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE translation_id_seq OWNED BY translation.id;


--
-- Name: trigger_information; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE trigger_information (
    id integer NOT NULL,
    key text NOT NULL,
    value text
);


ALTER TABLE public.trigger_information OWNER TO postgres;

--
-- Name: trigger_information_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE trigger_information_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.trigger_information_id_seq OWNER TO postgres;

--
-- Name: trigger_information_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE trigger_information_id_seq OWNED BY trigger_information.id;


--
-- Name: tt_event; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE tt_event (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    ttid integer NOT NULL,
    uid integer NOT NULL,
    ttevent text NOT NULL,
    ttstart timestamp without time zone,
    ttstop timestamp without time zone,
    cleared integer
);


ALTER TABLE public.tt_event OWNER TO postgres;

--
-- Name: tt_parts; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE tt_parts (
    eid integer,
    qty numeric(10,3),
    parts_id integer,
    parts_txt text
);


ALTER TABLE public.tt_parts OWNER TO postgres;

--
-- Name: units; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE units (
    name character varying(20) NOT NULL,
    base_unit character varying(20),
    factor numeric(20,5),
    type character varying(20),
    sortkey integer NOT NULL,
    id integer NOT NULL
);


ALTER TABLE public.units OWNER TO postgres;

--
-- Name: units_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE units_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.units_id_seq OWNER TO postgres;

--
-- Name: units_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE units_id_seq OWNED BY units.id;


--
-- Name: units_language; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE units_language (
    unit character varying(20) NOT NULL,
    language_id integer NOT NULL,
    localized character varying(20),
    localized_plural character varying(20),
    id integer NOT NULL
);


ALTER TABLE public.units_language OWNER TO postgres;

--
-- Name: units_language_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE units_language_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.units_language_id_seq OWNER TO postgres;

--
-- Name: units_language_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE units_language_id_seq OWNED BY units_language.id;


--
-- Name: vendor; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE vendor (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    name text NOT NULL,
    department_1 text,
    department_2 text,
    street text,
    zipcode text,
    city text,
    country text,
    contact text,
    phone text,
    fax text,
    homepage text,
    email text,
    notes text,
    terms smallint DEFAULT 0,
    taxincluded boolean,
    vendornumber text,
    cc text,
    bcc text,
    business_id integer,
    taxnumber text,
    discount real,
    creditlimit numeric(15,5),
    account_number text,
    bank_code text,
    bank text,
    language text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    obsolete boolean DEFAULT false,
    username text,
    user_password text,
    salesman_id integer,
    v_customer_id text,
    language_id integer,
    payment_id integer,
    taxzone_id integer NOT NULL,
    greeting text,
    ustid text,
    iban text,
    bic text,
    direct_debit boolean DEFAULT false,
    depositor text,
    delivery_term_id integer,
    currency_id integer NOT NULL,
    owener integer,
    employee integer,
    kundennummer character varying(20),
    sw text,
    branche character varying(45),
    grafik character varying(5),
    sonder integer,
    bland integer,
    lead integer,
    leadsrc character varying(25),
    konzern integer,
    headcount integer
);


ALTER TABLE public.vendor OWNER TO postgres;

--
-- Name: warehouse; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE warehouse (
    id integer DEFAULT nextval(('id'::text)::regclass) NOT NULL,
    description text,
    itime timestamp without time zone DEFAULT now(),
    mtime timestamp without time zone,
    sortkey integer,
    invalid boolean
);


ALTER TABLE public.warehouse OWNER TO postgres;

--
-- Name: wiedervorlage; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wiedervorlage (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    initdate timestamp without time zone NOT NULL,
    changedate timestamp without time zone,
    finishdate timestamp without time zone,
    cause text,
    descript text,
    document integer,
    status integer,
    kontakt character(1),
    employee integer,
    gruppe boolean DEFAULT false,
    initemployee integer,
    kontaktid integer,
    kontakttab character(1),
    tellid integer
);


ALTER TABLE public.wiedervorlage OWNER TO postgres;

--
-- Name: wissencategorie; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wissencategorie (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    name character varying(60),
    hauptgruppe integer,
    kdhelp boolean
);


ALTER TABLE public.wissencategorie OWNER TO postgres;

--
-- Name: wissencontent; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE wissencontent (
    id integer DEFAULT nextval(('crmid'::text)::regclass) NOT NULL,
    initdate timestamp without time zone NOT NULL,
    content text,
    employee integer,
    owener integer,
    version integer,
    categorie integer
);


ALTER TABLE public.wissencontent OWNER TO postgres;

SET search_path = tax, pg_catalog;

--
-- Name: report_categories; Type: TABLE; Schema: tax; Owner: postgres; Tablespace: 
--

CREATE TABLE report_categories (
    id integer NOT NULL,
    description text,
    subdescription text
);


ALTER TABLE tax.report_categories OWNER TO postgres;

--
-- Name: report_headings; Type: TABLE; Schema: tax; Owner: postgres; Tablespace: 
--

CREATE TABLE report_headings (
    id integer NOT NULL,
    category_id integer NOT NULL,
    type text,
    description text,
    subdescription text
);


ALTER TABLE tax.report_headings OWNER TO postgres;

--
-- Name: report_variables; Type: TABLE; Schema: tax; Owner: postgres; Tablespace: 
--

CREATE TABLE report_variables (
    id integer NOT NULL,
    "position" text NOT NULL,
    heading_id integer,
    description text,
    taxbase text,
    dec_places text,
    valid_from date
);


ALTER TABLE tax.report_variables OWNER TO postgres;

SET search_path = public, pg_catalog;

--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY background_job_histories ALTER COLUMN id SET DEFAULT nextval('background_job_histories_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY background_jobs ALTER COLUMN id SET DEFAULT nextval('background_jobs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY csv_import_profile_settings ALTER COLUMN id SET DEFAULT nextval('csv_import_profile_settings_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY csv_import_profiles ALTER COLUMN id SET DEFAULT nextval('csv_import_profiles_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY csv_import_report_rows ALTER COLUMN id SET DEFAULT nextval('csv_import_report_rows_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY csv_import_report_status ALTER COLUMN id SET DEFAULT nextval('csv_import_report_status_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY csv_import_reports ALTER COLUMN id SET DEFAULT nextval('csv_import_reports_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY currencies ALTER COLUMN id SET DEFAULT nextval('currencies_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY datev ALTER COLUMN id SET DEFAULT nextval('datev_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY defaults ALTER COLUMN id SET DEFAULT nextval('defaults_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY event_category ALTER COLUMN id SET DEFAULT nextval('event_category_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY events ALTER COLUMN id SET DEFAULT nextval('events_tmp_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY exchangerate ALTER COLUMN id SET DEFAULT nextval('exchangerate_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY finanzamt ALTER COLUMN id SET DEFAULT nextval('finanzamt_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY follow_up_access ALTER COLUMN id SET DEFAULT nextval('follow_up_access_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY generic_translations ALTER COLUMN id SET DEFAULT nextval('generic_translations_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY inventory ALTER COLUMN id SET DEFAULT nextval('inventory_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY price_rule_items ALTER COLUMN id SET DEFAULT nextval('price_rule_items_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY price_rules ALTER COLUMN id SET DEFAULT nextval('price_rules_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY prices ALTER COLUMN id SET DEFAULT nextval('prices_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_participants ALTER COLUMN id SET DEFAULT nextval('project_participants_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_phase_participants ALTER COLUMN id SET DEFAULT nextval('project_phase_participants_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_phases ALTER COLUMN id SET DEFAULT nextval('project_phases_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_roles ALTER COLUMN id SET DEFAULT nextval('project_roles_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_statuses ALTER COLUMN id SET DEFAULT nextval('project_status_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_types ALTER COLUMN id SET DEFAULT nextval('project_types_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY record_links ALTER COLUMN id SET DEFAULT nextval('record_links_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_acceptance_statuses ALTER COLUMN id SET DEFAULT nextval('requirement_spec_acceptance_statuses_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_complexities ALTER COLUMN id SET DEFAULT nextval('requirement_spec_complexities_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_items ALTER COLUMN id SET DEFAULT nextval('requirement_spec_items_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_orders ALTER COLUMN id SET DEFAULT nextval('requirement_spec_orders_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_parts ALTER COLUMN id SET DEFAULT nextval('requirement_spec_parts_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_pictures ALTER COLUMN id SET DEFAULT nextval('requirement_spec_pictures_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_predefined_texts ALTER COLUMN id SET DEFAULT nextval('requirement_spec_predefined_texts_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_risks ALTER COLUMN id SET DEFAULT nextval('requirement_spec_risks_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_statuses ALTER COLUMN id SET DEFAULT nextval('requirement_spec_statuses_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_text_blocks ALTER COLUMN id SET DEFAULT nextval('requirement_spec_text_blocks_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_types ALTER COLUMN id SET DEFAULT nextval('requirement_spec_types_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_versions ALTER COLUMN id SET DEFAULT nextval('requirement_spec_versions_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_specs ALTER COLUMN id SET DEFAULT nextval('requirement_specs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY status ALTER COLUMN id SET DEFAULT nextval('status_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY taxzone_charts ALTER COLUMN id SET DEFAULT nextval('taxzone_charts_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY todo_user_config ALTER COLUMN id SET DEFAULT nextval('todo_user_config_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY translation ALTER COLUMN id SET DEFAULT nextval('translation_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY trigger_information ALTER COLUMN id SET DEFAULT nextval('trigger_information_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY units ALTER COLUMN id SET DEFAULT nextval('units_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY units_language ALTER COLUMN id SET DEFAULT nextval('units_language_id_seq'::regclass);


--
-- Data for Name: acc_trans; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY acc_trans (acc_trans_id, trans_id, chart_id, amount, transdate, gldate, source, cleared, fx_transaction, ob_transaction, cb_transaction, project_id, memo, taxkey, itime, mtime, chart_link, tax_id) FROM stdin;
\.


--
-- Name: acc_trans_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('acc_trans_id_seq', 1, true);


--
-- Data for Name: ap; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY ap (id, invnumber, transdate, gldate, vendor_id, taxincluded, amount, netamount, paid, datepaid, duedate, invoice, ordnumber, notes, employee_id, quonumber, intnotes, department_id, itime, mtime, shipvia, cp_id, language_id, payment_id, storno, taxzone_id, type, orddate, quodate, globalproject_id, storno_id, transaction_description, direct_debit, deliverydate, delivery_term_id, currency_id) FROM stdin;
\.


--
-- Data for Name: ar; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY ar (id, invnumber, transdate, gldate, customer_id, taxincluded, amount, netamount, paid, datepaid, duedate, deliverydate, invoice, shippingpoint, terms, notes, ordnumber, employee_id, quonumber, cusordnumber, intnotes, department_id, shipvia, itime, mtime, cp_id, language_id, payment_id, delivery_customer_id, delivery_vendor_id, storno, taxzone_id, shipto_id, type, dunning_config_id, orddate, quodate, globalproject_id, salesman_id, marge_total, marge_percent, storno_id, transaction_description, donumber, invnumber_for_credit_note, direct_debit, delivery_term_id, currency_id) FROM stdin;
\.


--
-- Data for Name: assembly; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY assembly (id, parts_id, qty, bom, itime, mtime, assembly_id) FROM stdin;
\.


--
-- Name: assembly_assembly_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('assembly_assembly_id_seq', 1, false);


--
-- Data for Name: background_job_histories; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY background_job_histories (id, package_name, run_at, status, result, error, data) FROM stdin;
\.


--
-- Name: background_job_histories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('background_job_histories_id_seq', 1, false);


--
-- Data for Name: background_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY background_jobs (id, type, package_name, last_run_at, next_run_at, data, active, cron_spec) FROM stdin;
1	interval	CleanBackgroundJobHistory	\N	2015-02-02 03:00:00	\N	t	0 3 * * *
3	interval	BackgroundJobCleanup	\N	2015-02-02 03:00:00	\N	t	0 3 * * *
4	interval	SelfTest	\N	2015-02-02 02:20:00	\N	t	20 2 * * *
2	interval	CreatePeriodicInvoices	\N	2015-02-02 03:00:00	\N	t	0 3 * * *
5	interval	CleanAuthSessions	\N	2015-02-02 06:30:00	\N	t	30 6 * * *
\.


--
-- Name: background_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('background_jobs_id_seq', 5, true);


--
-- Data for Name: bank_accounts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY bank_accounts (id, account_number, bank_code, iban, bic, bank, chart_id, name) FROM stdin;
\.


--
-- Data for Name: bin; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY bin (id, warehouse_id, description, itime, mtime) FROM stdin;
\.


--
-- Data for Name: buchungsgruppen; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY buchungsgruppen (id, description, inventory_accno_id, sortkey) FROM stdin;
860	Standard 7%	53	2
859	Standard 19%	53	1
\.


--
-- Data for Name: bundesland; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY bundesland (id, country, bundesland) FROM stdin;
36	D  	Baden-Württemberg
37	D  	Bayern
38	D  	Berlin
39	D  	Brandenburg
40	D  	Bremen
41	D  	Hamburg
42	D  	Hessen
43	D  	Mecklenburg-Vorpommern
44	D  	Niedersachsen
45	D  	Nordrhein-Westfalen
46	D  	Rheinland-Pfalz
47	D  	Saarland
48	D  	Sachsen
49	D  	Sachsen-Anhalt
50	D  	Schleswig-Holstein
51	D  	Thüringen
52	CH 	Aargau
53	CH 	Appenzell Ausserrhoden
54	CH 	Appenzell Innerrhoden
55	CH 	Basel-Landschaft
56	CH 	Basel-Stadt
57	CH 	Bern
58	CH 	Freiburg
59	CH 	Genf
60	CH 	Glarus
61	CH 	Graubünden
62	CH 	Jura
63	CH 	Luzern
64	CH 	Neuenburg
65	CH 	Nidwalden
66	CH 	Obwalden
67	CH 	Schaffhausen
68	CH 	Schwyz
69	CH 	Solothurn
70	CH 	St. Gallen
71	CH 	Tessin
72	CH 	Thurgau
73	CH 	Uri
74	CH 	Waadt
75	CH 	Wallis
76	CH 	Zug
77	CH 	Zürich
78	A  	Burgenland
79	A  	Kärnten
80	A  	Niederösterreich
81	A  	Oberösterreich
82	A  	Salzburg
83	A  	Steiermark
84	A  	Tirol
85	A  	Vorarlberg
86	A  	Wien
\.


--
-- Data for Name: business; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY business (id, description, discount, customernumberinit, salesman, itime, mtime) FROM stdin;
892	Privatkunden	0	5000	f	2015-02-01 16:32:11.221773	\N
893	Geschäftskunde	0.300000012	10000	f	2015-02-01 16:32:36.981068	\N
\.


--
-- Data for Name: chart; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY chart (id, accno, description, charttype, category, link, taxkey_id, pos_bwa, pos_bilanz, pos_eur, datevautomatik, itime, mtime, new_chart_id, valid_from) FROM stdin;
1	0027	EDV-Software	A	A	AP_amount	9	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
2	0090	Geschäftsbauten	A	A	AP_amount	9	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
3	0200	Techn.Anlagen und Maschinen	A	A	AP_amount	9	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
4	0210	Maschinen	A	A	AP_amount	9	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
5	0380	Sonst.Transportmittel	A	A	AP_amount	9	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
74	4260	Instandhaltung betrieb.Räume	A	E	AP_amount	9	11	\N	13	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
68	4200	Raumkosten	A	E	AP_amount	9	11	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
69	4210	Miete	A	E	AP_amount	9	11	\N	11	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
70	4220	Pacht	A	E	AP_amount	0	\N	\N	11	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
71	4230	Heizung	A	E	AP_amount	9	11	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
72	4240	Gas,Strom, Wasser	A	E	AP_amount	9	11	\N	12	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
339	2670	Diskonterträge	A	I		0	32	\N	4	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
73	4250	Reinigung	A	E	AP_amount	9	11	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
75	4280	Sonstige Raumkosten	A	E	AP_amount	9	11	\N	13	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
76	4301	Nicht abziehbare Vorsteuer 7%	A	E	AP_paid	0	20	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
78	4320	Gewerbesteuer	A	E		0	12	\N	31	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
79	4340	Sonstige Betriebssteuern	A	E		0	12	\N	31	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
80	4350	Verbrauchssteuer	A	E		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
81	4355	Ökosteuer	A	E		0	12	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
82	4396	Steuerl.abzugs.Verspätungszuschl.u.Zwangsgelder	A	E		0	\N	\N	31	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
47	3790	Erhaltene Rabatte 16%/19% Vorsteuer	A	I	AP_paid	9	4	\N	\N	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
6	0400	Betriebsausstattung	A	A	AP_amount	9	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
7	0410	Geschäftsausstattung	A	A	AP_amount	9	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
8	0420	Büroeinrichtung	A	A	AP_amount	9	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
9	0430	Ladeneinrichtung	A	A	AP_amount	9	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
10	0440	Werkzeuge	A	A	AP_amount	9	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
11	0480	Geringwertige Wirtschaftsg.b.410EUR	A	A	AP_amount	9	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
12	1200	Bank	A	A	AR_paid:AP_paid	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
13	1360	Geldtransit	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
14	1400	Ford. a.Lieferungen und Leistungen	A	A	AR	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
15	1590	Durchlaufende Posten	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
16	1600	Verbindlichkeiten aus Lief.u.Leist.	A	L	AP	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
17	1780	Umsatzsteuer-Vorauszahlungen	A	E		0	\N	\N	28	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
18	1790	Umsatzsteuer Vorjahr	A	E		0	\N	\N	28	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
19	1791	Umsatzsteuer frühere Jahre	A	E		0	\N	\N	28	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
20	1800	Privatentnahme allgemein	A	Q		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
21	1810	Privatsteuern	A	Q		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
22	1820	Sonderausgaben beschränkt abzugsfähig	A	Q		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
23	1830	Sonderausgaben unbeschr.anzugsfähig	A	Q		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
24	1840	Zuwendungen, Spenden	A	Q		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
25	1890	Privateinlagen	A	Q		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
26	2110	Zinsaufwendung f.kurzf.Verbindlichk	A	E		0	30	\N	29	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
27	2120	Zinsaufwendung f.langf.Verbindlichk	A	E		0	30	\N	29	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
28	2130	Diskontaufwendung	A	E		0	30	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
29	2310	Anlagenabgänge Sachanlagen Restbuchw.b.Buchverlust	A	E		0	31	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
30	2315	Anlagenabgänge Sachanlagen Restbuchw.b.Buchgewinn	A	I		0	31	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
31	2320	Verluste Abgang Gegenst.d.Anlagever	A	E		0	31	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
32	2650	Sonstige Zinsen und änliche Erträge	A	I		0	32	\N	4	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
33	2720	Ertr.a.d.Abgang v.Gegens.d.Anlageve	A	I		0	33	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
297	3650	Nicht abziehb.Vorsteuer 16%/19%	A	E		0	5	\N	8	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
291	1592	Fremdgeld	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
42	3731	Erhaltene Skonti 7% Vorsteuer	A	E	AP_paid	8	4	\N	\N	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.364595	\N	\N
43	3735	Erhaltene Skonti 16%/19% Vorsteuer	A	E	AP_paid	9	4	\N	\N	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.364595	\N	\N
34	2742	Versicherungsentschädigungen	A	I		0	33	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
35	3000	Roh,-Hilfs,-und Betriebsstoffe	A	E	IC	9	4	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
36	3300	Wareneingang 7% Vorsteuer	A	E	AP_amount:IC_cogs	8	4	\N	8	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
37	3400	Wareneingang 16%/19% Vorsteuer	A	E	AP_amount:IC_cogs	9	4	\N	8	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
41	3550	Steuerfreier innergem.Erwerb	A	E	AP_amount:IC_cogs	0	\N	\N	8	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
44	3750	Erhaltene Boni 7% Vorsteuer	A	I	AP_paid	8	4	\N	\N	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
46	3780	Erhaltene Rabatte 7% Vorsteuer	A	I	AP_paid	8	4	\N	\N	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
48	3800	Bezugsnebenkosten	A	E	AP_amount:IC_cogs:IC_expense	0	4	\N	8	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
49	3830	Leergut	A	E	AP_amount:IC_cogs	0	4	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
50	3850	Zölle und Einfuhrabgaben	A	E	AP_amount:IC_cogs	0	4	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
51	3960	Bestandsver.Roh-,Hilfs-.Betriebs.sow.bez.Waren	A	E		0	2	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
52	3970	Bestand Roh-,Hilfs-,u.Betriebsstoff	A	A	IC	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
53	3980	Bestand Waren	A	A	IC	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
161	8731	Gewährte Skonti 7% USt	A	I	AR_paid	2	1	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.364595	\N	\N
353	2700	Sonstige Erträge	A	I		3	33	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
54	4000	Material-und Stoffverbrauch	A	E	IC_cogs	0	20	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
55	4110	Löhne	A	E		0	10	\N	9	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
56	3990	Verrechnete Stoffkosten	A	E	IC_cogs	0	4	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
57	4120	Gehälter	A	E		0	10	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
58	4125	Ehegattengehalt	A	E		0	10	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
59	4138	Beiträge zur Berufsgenossenschaft	A	E		0	10	\N	10	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
60	4139	Ausgleichsabgabe i.S.d.Schwerbehinterdengesetzes	A	E		0	10	\N	10	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
61	4140	Freiwillig soziale Aufwendungen lohnsteuerfrei	A	E		0	10	\N	10	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
62	4145	Freiwillige sozi.Aufw.lohnsteuerpflichtig.	A	E		0	10	\N	10	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
63	4149	Pauschale Lohnsteuera.sons.Bezüge (z.B.Fahrtkostenzu)	A	E		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
64	4150	Krankengeldzuschüsse	A	E		0	10	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
65	4175	Fahrtkostenerst.Wohnung/Arbeitsstät	A	E		0	10	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
66	4190	Aushilfslöhne	A	E		0	10	\N	9	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
67	4199	Lohnsteuer für Aushilfe	A	E		0	10	\N	10	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
195	1775	Umsatzsteuer 16%	A	I	AR_tax:IC_taxpart:IC_taxservice	0	\N	\N	6	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
88	8300	Erlöse 7%USt	A	I	AR_amount:IC_sale:IC_income	2	1	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
150	8310	Erlöse Inland stpfl. EG-Lieferung7%	A	I	AR_amount:IC_sale:IC_income	12	1	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
154	8506	Provisionserlöse 7% USt.	A	I	AR_amount:IC_income	2	\N	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
158	8591	Sachbezüge 7% Ust (Waren)	A	I	AR_amount	2	5	\N	2	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
163	8750	Gewährte Boni 7% USt.	A	E	AR_paid	2	1	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
83	4397	Steuerl.n.abzugsf.Verspätungszuschläge u.Zwangsgelder	A	E		0	\N	\N	31	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
84	4500	Fahrzeugkosten	A	E	AP_amount	0	\N	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
85	4530	Laufende Kfz-Betriebskosten	A	E	AP_amount	9	14	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
40	3440	Innergem.Erwerb v.Neufahrz.v.Lief.o.USt.Identnr.16%/19%VSt.u.16%/19%USt.	A	E	AP_amount:IC_cogs	19	\N	\N	8	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
45	3760	Erhaltene Boni 16%/19% Vorsteuer	A	I	AP_paid	9	4	\N	\N	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
94	4610	Werbekosten	A	E	AP_amount	9	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
89	4540	Kfz-Reparaturen	A	E	AP_amount	9	14	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
90	4550	Garagenmiete	A	E	AP_amount	0	14	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
91	4570	Leasingfahrzeugkosten	A	E	AP_amount	0	14	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
92	4580	Sonstige Kfz-Kosten	A	E	AP_amount	9	14	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
93	4600	Werbe-und Reisekosten	A	E	AP_amount	9	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
95	4638	Geschenke ausschließl.betrieb.genut	A	E	AP_amount	9	\N	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
96	4640	Repräsentationskosten	A	E	AP_amount	9	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
97	4650	Bewirtungskosten	A	E	AP_amount	9	20	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
98	4653	Aufmerksamkeiten	A	E	AP_amount	9	\N	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
99	4654	Nicht abzugsfähige Bewirtungskosten	A	E	AP_amount	0	\N	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
100	4660	Reisekosten Arbeitnehmer	A	E	AP_amount	9	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
101	4663	Reisekosten Arbeitn.m.Vorsteuerabzu f.öffentl.Verkehrsm.	A	E	AP_amount	8	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
102	4664	Reisekosten Arbeitn.Verpflegungmehr	A	E	AP_amount	0	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
103	4666	Reisekosten Arbeitn.Übernachtungsaufwand	A	E	AP_amount	0	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
104	4668	Kilometerentgelderstattung Arbeitnehmer	A	E	AP_amount	0	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
105	4670	Reisekosten Unternehmer	A	E	AP_amount	9	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
106	4673	Reisek.Untern.m.Vorsteuerabzug (öffentl.Verkehrsm.	A	E	AP_amount	8	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
107	4674	Reisekosten Untern.Verpflegungsmehr	A	E	AP_amount	0	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
108	4676	Reisekosten Untern.Übernachtungsauf	A	E	AP_amount	0	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
109	4700	Kosten der Warenabgabe	A	E	AP_amount:IC_cogs	0	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
110	4710	Verpackungsmaterial	A	E	AP_amount:IC_cogs	0	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
111	4730	Ausgangsfracht	A	E	AP_amount:IC_cogs	0	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
112	4750	Transportversicherung	A	E	AP_amount:IC_cogs:IC_expense	0	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
113	4760	Verkaufsprovision	A	E	AP_amount:IC_expense	0	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
114	4780	Fremdarbeiten	A	E	AP_amount:IC_expense	0	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
115	4790	Aufwand für Gewährleistungen	A	E	AP_amount:IC_expense	0	20	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
116	4800	Rep.u.Instandhaltungen v.techn.Anlagen u.Maschinen	A	E	AP_amount	9	18	\N	19	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
117	4806	Wartungskosten für Hard-u.Software	A	E	AP_amount:IC_expense	9	18	\N	19	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
118	4809	Sonstige Reparaturen u.Instandhalt.	A	E	AP_amount	9	\N	\N	19	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
119	4810	Mietleasing	A	E	AP_amount	0	20	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
120	4815	Kaufleasing	A	E	AP_amount	0	20	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
121	4822	Abschreibungen a.immat.Vermögensgeg	A	E		0	\N	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
122	4824	Abschreibung a.d.Geschäft-o.Firmenw	A	E		0	\N	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
123	4840	Außerplanmäßig Abschr.a.Sachanlagen	A	E		0	\N	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
124	4855	Sofortabschreibung GWG	A	E	AP_amount	0	\N	\N	26	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
125	4860	Abschreibungen auf aktivierte GWG	A	E		0	\N	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
126	4900	Sonstige betriebliche Aufwendungen	A	E	AP_amount	9	20	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
127	4905	Sons.Aufw.betriebl. und regelmäßig	A	E	AP_amount	9	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
128	4909	Fremdleistungen	A	E	AP_amount	0	20	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
129	4910	Porto	A	E	AP_amount	0	20	\N	23	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
130	4920	Telefon	A	E	AP_amount	9	20	\N	23	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
131	4925	Telefax	A	E	AP_amount	9	20	\N	23	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
132	4930	Bürobedarf	A	E	AP_amount	9	20	\N	23	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
133	4940	Zeitschriften, Bücher	A	E	AP_amount	8	20	\N	20	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
134	4945	Fortbildungskosten	A	E	AP_amount	9	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
135	4946	Freiwillige Sozialleistungen	A	E	AP_amount	0	\N	\N	9	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
136	4950	Rechts- und Beratungskosten	A	E	AP_amount	9	20	\N	22	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
137	4955	Buchführungskosten	A	E	AP_amount	9	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
138	4957	Abschluß- und Prüfungskosten	A	E	AP_amount	9	20	\N	22	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
139	4960	Mieten für Einrichtungen	A	E	AP_amount	9	20	\N	21	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
140	4969	Aufwend.f.Abraum-u.Abfallbeseitigung	A	E	AP_amount	9	20	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
141	4970	Nebenkosten des Geldverkehrs	A	E		0	20	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
142	4980	Betriebsbedarf	A	E	AP_amount	9	20	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
143	4985	Werkzeuge und Kleingeräte	A	E	AP_amount	9	\N	\N	19	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
77	4305	Nicht abziehbare Vorsteuer 16%/19%	A	E	AP_paid	0	20	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
155	8508	Provisionserlöse 16%/19% USt.	A	I	AR_amount:IC_income	3	\N	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
167	8820	Erlöse aus Anlageverkäufen Sachanl.verm.b.Buchgewinn16%/19%Ust	A	I	AR_amount	3	5	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
171	8910	Entnahme des Untern.f.Zwecke ausserh.d.Untern 16%/19%(Waren)	A	I	AR_amount	3	5	\N	3	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
149	8200	Erlöse	A	I	AR_amount:IC_sale:IC_income	0	1	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
175	8925	Unentgeltl.Erbring.sons.Leis.16%/19%USt	A	I	AR_amount	3	\N	\N	3	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
177	8935	Unentgeltl.Zuwend.v.Gegens. 16%/19% Ust	A	I	AR_amount	3	\N	\N	3	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
152	8320	Erlöse aus im and.EG-Land steuerpfl.Lieferungen	A	I	AR_amount:IC_sale:IC_income	10	1	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
153	8500	Provisionserlöse	A	I	AR_amount:IC_income	3	5	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
184	8400	Erlöse 16%/19% USt.	A	I	AR_amount:IC_sale:IC_income	3	1	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
156	8520	Erlöse Abfallverwertung	A	I	AR_amount:IC_sale	3	\N	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
157	8540	Erlös Leergut	A	I	AR_amount:IC_sale	3	5	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
255	8921	Verwend.v.Gegenst.f.Zwecke außerh.d.Untern.16%/19%USt(Kfz-Nutzung)	A	I		0	5	\N	3	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
256	8922	Verwend.f.Gegenst.f.Zwecke außerh.d.Untern.16%/19%USt(Telefonnutzung)	A	I		0	5	\N	3	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
275	8809	Erlöse aus Verk.Sachanlagevermögen16%/19% USt (bei Buchverlust)	A	E		0	\N	\N	2	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
862	1570	Anrechenbare Vorsteuer	A	E	AP_tax:IC_taxpart:IC_taxservice	0	\N	\N	\N	f	2015-02-01 15:52:37.870408	2015-02-01 15:52:39.18863	\N	\N
863	1574	Abziehbare Vorsteuer aus innergem. Erwerb 19 %	A	E	AP_tax:IC_taxpart:IC_taxservice	19	\N	\N	\N	f	2015-02-01 15:52:37.870408	2015-02-01 15:52:39.18863	\N	\N
864	1774	Umsatzsteuer aus innergem. Erwerb 19 %	A	I	AR_tax:IC_taxpart:IC_taxservice	0	\N	\N	\N	f	2015-02-01 15:52:37.870408	2015-02-01 15:52:39.18863	\N	\N
168	8829	Erl.a.Anlagenverk.bei Buchgewinn	A	I	AR_amount	3	\N	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
169	8900	Unentgeltliche Wertabgaben	A	I	AR_amount	0	\N	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
170	8905	Entnahme v. Gegenst.o.USt.	A	I		0	5	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
173	8919	Entnahme durch den Untern.f.Zwecke außerhalb d.Untern.(Waren)o.USt	A	I		0	5	\N	3	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
174	8924	Verwendung v.Gegenständen f.Zwecke außerhalb d.Untern.o.USt.	A	I		0	\N	\N	3	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
179	8955	Umsatzsteuervergütungen	A	I		0	\N	\N	7	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
180	9000	Saldenvorträge,Sachkonten	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
181	9008	Saldenvorträge,Debitoren	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
182	9009	Saldenvorträge,Kreditoren	A	L		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
183	9090	Summenvortragskonto	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
185	8800	Erlöse aus Anlagenverkäufen	A	I	AR_amount	3	\N	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
186	4380	Beiträge	A	E	AP_amount	0	\N	\N	14	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
187	4360	Versicherungen	A	E	AP_amount	0	13	\N	14	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
188	4390	Sonstige Abgaben	A	E	AP_amount	0	\N	\N	14	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
189	0631	Vblk.geg.Kreditinst.- Restlaufzeit b.1 Jahr	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
190	0640	Vblk.geg.Kreditinst.- Restlaufzeit 1 bis 5 Jahre	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
191	0650	Vblk.geg.Kreditinst.- Restlaufzeit grösser als 5 Jahre	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
192	4510	Kfz-Steuer	A	E	AP_amount	0	19	\N	15	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
193	4520	Kfz-Versicherungen	A	E	AP_amount	0	14	\N	16	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
196	1767	Im anderen EG-Staat steuerpfl. Lieferung	A	 		10	0	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
197	0853	Satzungsm.Rücklagen 0% Vorbelast.(st.Einlagekto.)	A	Q		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
198	1607	Vblk.a.LuL ohne Vorsteuer (EÜR)	A	L	AP	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
199	1609	Gegenkonto 1605-1607 b.Aufteilung d.Vblk.n.Steuers.(EÜR)	A	L		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
200	2125	Zinsaufwend.f.Gebäude,die z.Betriebsvermögen gehören	A	E		0	\N	\N	29	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
201	1445	Ford.a.LuL z.allg.USt-Satz o.eines Kleinuntern.(EÜR)	A	A	AR	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
202	1446	Ford.aLuL z.erm.USt.-Satz (EÜR)	A	A	AR	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
203	1447	Ford.a.steuerfr.od.nicht steuerb.LuL (EÜR)	A	A	AR	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
204	1448	Ford.a.LuL n.Durchschnittss.gem.§24UStG(EÜR)	A	A	AR	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
205	1449	Gegenkto. 1445-1448 bei Aufteil.d.Ford.n.Steuers.(EÜR)	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
206	1605	Vblk.a.LuL z.allg.Umsatzsteuersatz (EÜR)	A	L	AP	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
207	1606	Vblk.a.LuL zum erm.Umsatzsteuersatz (EÜR)	A	L	AP	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
208	2212	Kapitalertragssteuer 20%	A	E		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
209	2342	Einst.in SoPo m.Rücklageanteil (Existenzgründungsrücklage)	A	E		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
210	2351	Gründstücksaufwend.f.Gebäude,die nicht z.Betriebsverm.gehören	A	E		0	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
211	2376	Grundsteuer f.Gebäude,die nicht z.Betriebsvermögen geh.	A	E		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
212	2733	Erträge a.d.Auflösung v.SoPo m.Rücklageant.(Exitenzgründungszusch.)	A	I		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
213	2746	Steuerfr.Erträge a.d.Auflös.v.SoPo m.Rücklageanteil	A	I		0	\N	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
214	2747	Sonst.steuerfreie Betriebseinnahmen	A	I		0	\N	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
215	2797	Entnahmen a.satzungsmäßigen Rücklagen	A	E		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
216	3559	Steuerfreie Einfuhren	A	E	AP_amount:IC_cogs	0	\N	\N	8	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
217	3580	Wareneinkauf z.allg.Umsatzsteuersatz (EÜR)	A	E		0	4	\N	8	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
218	3581	Wareneinkauf z.erm.Umsatzsteuersatz(EÜR)	A	E		0	4	\N	8	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
219	3582	Wareneinkauf ohne Vorsteuerabzug(EÜR)	A	E		0	4	\N	8	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
220	3589	Gegenkto.3580-3582 b.Aufteilung d.WE n.Steuersätzen(EüR)	A	E		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
221	4261	Instandhlt.betriebl.Räume in Geb.die nicht z.BV gehören	A	E		0	\N	\N	11	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
222	4271	Abgaben f.betriebl.genutzten Grundbesitz,d.nicht z.BV gehört	A	E		0	\N	\N	11	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
223	4288	Aufwend.f.ein häusliches Arbeitszimmer(abziehb.Anteil)	A	E		0	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
224	4289	Aufwend.f.ein häusliches Arbeitszimmer(nicht abziehb.Anteil)	A	E		0	11	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
225	4361	Versicherungen f.Gebäude,die zum BV gehören	A	E		0	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
226	4505	Fahrzeugkosten f.Kfz,die nicht zum BV gehören	A	E		0	\N	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
227	4515	Kfz-Steuer f.Kfz,die nicht zum BV gehören	A	E		0	\N	\N	15	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
228	4525	Kfz-Versicherung f.Kfz,die nicht zum BV gehören	A	E		0	\N	\N	16	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
229	4535	Lfd.Kfz-Betriebskosten f.Kfz,die nicht zum BV gehören	A	E		0	\N	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
230	4545	Kfz-Rep.für Kfz,die nicht zum BV gehören	A	E		0	\N	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
231	4555	Garagenmiete f.Kfz,die nicht zum BV gehören	A	E		0	\N	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
232	4560	Mautgebühren	A	E		0	14	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
233	4565	Mautgebühren f. Kfz,die nicht zum BV gehören	A	E		0	\N	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
234	4651	Sonst.eingeschr.abziehb.Betriebsausgaben (abziehb.Anteil)	A	E		0	20	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
235	4652	Sonst.eingeschr.abziehb.Betriebsausgaben(nicht abziehb.Teil)	A	E		0	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
236	4678	Kilometergelderstatt.(Wohn.-Arbeitsst.abziehb.Teil)	A	E		0	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
237	4679	Fahrten zw.Wohn.und Arbeitsstätte (nicht abziehb.Teil)	A	E		0	\N	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
238	4680	Fahrten zw.Wohn.-und Arbeitsstätte (Haben)	A	E		0	\N	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
239	4831	Abschreibung auf Gebäude	A	E		0	17	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
240	4830	Abschreibungen auf Sachanlagen (o.Kfz u.Geb.)	A	E		0	17	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
241	4832	Abschreibungen auf Kfz	A	E		0	17	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
242	4841	Absetzung f.außergew.techn.u.wirtschaftl.AfA bei Gebäuden	A	E		0	17	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
243	4842	Absetzung f.außergew.techn.u.wirtschaftl.AfA des Kfz	A	E		0	17	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
244	4843	Absetzung f.außergew.techn.u.wirtschaftl.AfA sonst.WG	A	E		0	17	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
245	4851	Sonderabschreibung nach §7g(1)u.(2)EStG (ohne Kfz)	A	E		0	17	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
246	4852	Sonderabschreibung n.§7g(1)u.(2)EStG (für Kfz)	A	E		0	17	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
247	4965	Mietleasing	A	E		9	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
248	4966	Gewerbest.zu berücksicht.Mietleasing §8 GewStG	A	E		0	\N	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
249	8580	Erlöse zum allg.Umsatzsteuersatz (EÜR)	A	I		0	1	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
250	8581	Erlöse zum ermä.Umsatzsteuersatz (EÜR)	A	I		0	1	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
251	8582	Erlöse steuerfrei u.nicht steuerbar (EÜR)	A	I		0	1	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
252	8589	Gegenkonto 8580-8582 b.Aufteilung d.Erlöse n.Steuersätzen(EÜR)	A	C		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
257	1000	Kasse	A	A	AR_paid:AP_paid	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
264	2501	Ausserordentliche Erträge finanzwirksam	A	I		0	\N	\N	5	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
265	2505	Ausserordentliche Erträge nicht finanzwirksam	A	I		0	\N	\N	5	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
266	4130	Gesetzlich soziale Aufwendungen	A	E		0	10	\N	10	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
267	4630	Geschenke bis 35 EUR abzugsfähig	A	E	AP_amount	0	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
268	4635	Geschenke über 35EUR nicht abzugsf.	A	E	AP_amount	0	15	\N	18	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
269	4655	Nicht abzugsf.Betriebsausg.a.Werbe-Repräsentatisonk.etc.	A	E	AP_amount	9	15	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
270	4805	Rep.u.Instandhalt.v.and.Anlagen u.Betriebs-u.Geschäftsausst.	A	E	AP_amount	9	18	\N	19	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
272	8195	Erlöse als Kleinunternehmer i.S.d.§19(1)UStG	A	I	AR_amount	0	1	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
331	2175	Nicht abziehbare Vorsteuer 16%/19%	A	E		0	31	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
277	8960	Bestandsveränderung unf.Erz.	A	E		0	2	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
278	8970	Bestandsveränd.unf.Leist.	A	E		0	2	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
279	8980	Bestandsveränd.fert..Leist.	A	E		0	2	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
280	8990	And.aktiv.Eigenleistungen	A	E		0	3	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
281	3090	Energiestoffe (Fert.).	A	E	IC	9	4	\N	8	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
282	4595	Fremdfahrzeuge	A	E	AP_amount	0	14	\N	17	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
283	1450	Ford.a.LuL n.Durchschnittss.gem.§24UStG(EÜR)	A	A	AR	0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
286	1580	Gegenkonto Vorsteuer §4/3 EStG	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
287	1581	Auflösung Vorst.a.Vorjahr §4/3 EStG	A	A		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
288	1582	Vorst.aus Investitionen §4/3 EStG	A	E		0	\N	\N	27	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
289	1584	abziehb.VorSt IG Erwerb Neufzg.b.Lief. o. USt.Ident.Nr	A	E	AR_tax	0	\N	\N	27	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
292	3100	Fremdleistungen	A	E	AP_amount	9	5	\N	8	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
295	3600	Nicht abziehbare Vorsteuer	A	E		0	5	\N	8	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
296	3610	Nicht abziehb.Vorsteuer 7%	A	E		0	5	\N	8	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
298	2739	Erträge Aufl. Sopo m.R.(Ansparafa)	A	I		0	5	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
299	8590	Verrechnete sons. Sachbezüge keine Waren	A	I	AR_amount	0	5	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
301	8939	Unentgeltl.Zuw.v.Gegens.ohne Ust	A	I		0	5	\N	3	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
304	8949	Unentgeltl.Zuwendung v.Waren o.USt.	A	I		0	5	\N	3	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
305	4126	Tantiemen	A	E		0	10	\N	9	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
306	4127	Geschäftsführergehälter	A	E		0	10	\N	9	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
307	4167	Pauschale Lohnsteuer auf sonst.Bezüge(z.B.Direktversicherung	A	E		0	10	\N	9	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
308	4170	Vermögenswirksame Leistungen	A	E		0	10	\N	9	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
309	4180	Bedienungsgelder	A	E		0	10	\N	9	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
310	4826	Außerplan.AfA a.immat.Vermögensgeg.	A	E		0	17	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
311	4850	Abschr.a.Sachanl.aufgr.steuerl.Sondervorschriften	A	E		0	17	\N	25	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
312	4870	Abschreibungen auf Finanzanlagen	A	E		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
313	4875	Abschr.a.Wertp.d.Umlaufvermögens	A	E		0	17	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
314	4880	Abschreibungen auf Umlaufverm. o.Wertpapiere (soweit unübl.Höhe	A	E		0	17	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
315	2208	Solidaritätszuschlag	A	E		0	35	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
316	2209	Solidaritätszuschlag für Vorjahr	A	E		0	19	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
317	2375	Grundsteuer	A	E		0	19	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
318	2400	Forderungsverlust-übliche Höhe	A	E		0	20	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
321	2341	Einstellungen in SoPo m.Rücklageanteil (Ansparabschreibung	A	E		0	20	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
322	2100	Zinsen und ähnl.Aufwendungen	A	E		0	30	\N	29	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
323	2107	Zinsaufwendung-betriebliche Steuern §223aAO	A	E		0	30	\N	29	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
324	2140	Zinsähnliche Aufwendungen	A	E		0	30	\N	29	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
325	2000	Außerordentliche Aufwendung	A	E		0	31	\N	30	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
326	2010	Betriebsfremde Aufwendungen soweit n.außerord.	A	E		0	31	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
327	2020	Periodenfremde Aufwend.(soweit n.außerordentlich	A	E		0	31	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
328	2150	Aufwendungen aus Kursdifferenzen	A	E		0	31	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
329	2170	Nicht abziehbare Vorsteuer	A	E		0	31	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
330	2171	Nicht abziehbare Vorsteuer 7%	A	E		0	31	\N	24	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
332	2280	Stnachzahl.Vorj.v.Einkomm u.Ertrag	A	E		0	31	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
333	2285	Steuernachzahlung Vorj.f.sons.Steue	A	E		0	31	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
334	2289	Erträge a.d.Aufl.v. Rück.f.sons.Ste	A	E		0	31	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
335	2350	Grundstücksaufwendungen	A	E		9	31	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
336	2380	Zuwend.,Spenden,steuerlich n.abziehbar	A	E		0	31	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
337	2450	Einstell.i.d.Pauschalwertbe.z.Forde	A	E		0	31	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
338	2657	Zinserträge-betriebliche Steuern	A	I		0	32	\N	4	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
340	2680	Zinsähnliche Erträge	A	I		0	32	\N	4	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
341	8650	Erlöse Zinsen und Diskotspesen	A	I		0	32	\N	4	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
342	8700	Erlösschmälerungen	A	I		0	32	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
151	8315	Erlös Inland stpfl.EG-Lieferung 16%/19%	A	I	AR_amount:IC_sale:IC_income	13	1	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
159	8595	Sachbezüge 16%/19% Ust (Waren)	A	I	AR_amount	3	5	\N	2	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
347	8727	Erlösschmä.and.EG Land stpfl.Liefer	A	I		0	32	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
348	2500	Außerordentliche Erträge	A	I		0	33	\N	5	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
349	2510	Betriebsfremde Erträge nichtaußerorden	A	I		0	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
350	2520	Periodenfremde Erträge nicht außero	A	I		0	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
351	2600	Erträge aus Beteiligungen	A	I		0	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
352	2660	Erträge aus Kursdifferenzen	A	I		0	33	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
164	8760	Gewährte Boni 16%/19% USt.	A	E	AR_paid	3	1	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
354	2710	Ertrag a.d.Zuschreib.d.Anlagevermög	A	I		0	33	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
355	2715	Ertr.a.Zuschreib.d.Umlaufvermögens	A	I		0	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
356	2725	Ertr.a.d.Abg.v.Gegenst.d.Umlaufverm	A	I		0	33	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
357	2730	Ertr.a.Herabsetzung d.PWB zu Forderungen	A	I		0	33	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
358	2732	Ertr. aus abgeschriebenen Forderung	A	I		0	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
359	2735	Ertr.a.d.Auflösung v.Rückstellungen	A	I		0	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
360	2743	Investitionszuschüsse-steuerpflicht	A	I		0	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
361	2744	Investitionszulage-steuerfrei	A	I		0	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
362	2750	Grundstückserträge	A	I		3	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
363	2284	Ertr.a.d.Aufl.v.Rücks.f.St.v.Ein.Er	A	I		0	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
364	2287	Steuererstattung Vorj.f.sons.Steuer	A	I		0	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
365	2282	Sterstat.Vorj.Steuer v.Eink.u.Ertrag	A	I		0	33	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
166	8790	Gewährte Rabatte 16%/19% Ust.	A	E	AR_paid	3	1	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
367	4990	Kalkulatorischer Unternehmerlohn	A	E		0	34	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
368	4992	Kalkulatorische Zinsen	A	E		0	34	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
369	4993	Kalkulatorische Aschreibungen	A	E		0	34	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
370	4994	Kalkulatorische Wagnisse	A	E		0	34	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
371	4995	Kalkulatorischer Lohn f.unentgeltl.Mitarbeiter	A	E		0	34	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
372	2200	Körperschaftssteuer	A	E		0	35	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
373	2214	Anrechenb.Soli auf Kapitalertragssteuer 20%	A	E		0	35	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
374	2215	Zinsabschlagsteuer	A	E		0	35	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
375	2218	Anrechb.Solidari.zuschlag a.Zinsabschlagst.	A	E		0	35	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
775	1776	Umsatzsteuer 19 %	A	I	AR_tax:IC_taxpart:IC_taxservice	0	\N	\N	6	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
344	8720	Erlösschmälerung 16%/19% USt.	A	I		3	32	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
145	8125	Steuerfrei innergem. Lieferungen §41bUStG	A	I	AR_amount:IC_sale:IC_income	0	1	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
146	8130	Liefe.d.1.Abnehm.bei innergem.Dreiecksg §25b2UStG	A	I	AR_amount:IC_sale:IC_income	0	\N	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
254	8828	Erlöse a.Verk.Sachanlagen steuerfr.§4Nr.1bUStG(b.Buchgewinn)	A	I		0	\N	\N	2	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
274	8808	Erlöse a.Verk.Sachanlagen steuerfrei§4Nr.1bUStG(b.Buchverlust)	A	E		0	\N	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
147	8135	Steuerfr.innergem.Lief.v.Neufahrz.an Abn.o.USt-Ident-Nr.	A	I	AR_amount:IC_sale	0	\N	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
144	8120	Steuerfreie Umsätze §4Nr.1a UstG	A	I	AR_amount:IC_sale:IC_income	0	1	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
148	8150	Sonstige steuerfreie Umsätze §42-7UStG	A	I	AR_amount:IC_sale:IC_income	0	\N	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
253	8827	Erlöse a.Verk.Sachanlagen steuerfr.§4Nr.1aUStG(bei Buchgewinn)	A	I		0	\N	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
273	8807	Erlöse a.Verk.Sachanlagen steuerfrei§4Nr.1aUStG(b.Buchverlust)	A	E		0	\N	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
86	8100	Steuerfreie Umsätze §4Nr.8ff UstG	A	I	AR_amount	0	1	\N	\N	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
87	8110	Sonstige steuerfreie Umsätze Inland	A	I	AR_amount	0	\N	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
160	8600	Sonstige Erlöse betriebl.u.regelmäß	A	I	AR_amount	3	5	\N	2	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
165	8780	Gewährte Rabatte 7% USt.	A	E	AR_paid	2	1	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
172	8915	Entnah.d.Untern.f.Zwecke ausserh.d.Untern.7%USt	A	I	AR_amount	2	5	\N	3	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
176	8930	Verwendung v.Gegenst.f.Zwecke außerhalb d.Unternehmens 7% USt.	A	I	AR_amount	2	5	\N	3	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
303	8945	Unentgeltl.Zuw.von Waren 7% Ust	A	I		2	5	\N	3	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
319	2401	Forderungsverluste 7% USt.(übliche Höhe	A	E		2	20	\N	\N	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
343	8710	Erlösschmälerung 7% USt.	A	I		2	32	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
345	8725	Erlösschmä.Inl.stpfl.EG-Lief. 7%USt	A	I		12	32	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
271	8190	Erlöse,die mit den Durchschnittssätzen d.§24UStG vers.werd.	A	I	AR_amount	0	\N	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
178	8950	Nicht steuerbare Umsätze	A	I		0	5	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
293	3110	Leist.v.ausländ.Untern. 7% VSt.u.7%USt.	A	E	AP_amount	8	5	\N	8	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
366	1785	Umsatzsteuer n.§13b UStG	A	I		0	33	\N	7	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
290	1588	Bezahlte Einfuhrumsatzsteuer	A	E		0	\N	\N	\N	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
285	1578	Abzieb.Vorst.n.§13b	A	E	AP_tax:IC_taxpart:IC_taxservice	0	\N	\N	27	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
284	1577	Abzieb.Vorst.n.allg.Durchschnittss.UStVA Kz.63	A	E	AP_tax:IC_taxpart:IC_taxservice	0	\N	\N	27	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
259	1571	Abziehbare Vorsteuer 7%	A	E	AP_tax:IC_taxpart:IC_taxservice	0	\N	\N	27	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
261	1575	Abziehbare Vorsteuer 16%	A	E	AP_tax:IC_taxpart:IC_taxservice	0	\N	\N	27	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
776	1576	Abziehbare Vorsteuer 19 %	A	E	AP_tax:IC_taxpart:IC_taxservice	9	\N	\N	27	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
276	8801	Erlöse aus Anlagenverkäufen Sachanl.verm.b.Buchverl.16%/19%USt.	A	E		3	5	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
300	8920	Verw.v.Gegens.f.Zwecke ausserh.d.Untern.16%/19%USt.	A	I		3	5	\N	3	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
302	8940	Unentgeltl.Zuw.v Waren 16%/19% Ust	A	I		3	5	\N	3	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
320	2405	Forderungsverluste 16%/19% USt.(übliche Höhe	A	E		3	20	\N	\N	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
346	8726	Erlösschmä.Inl.stpfl.EG-Lief.16%/19%USt	A	I		13	32	\N	1	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
294	3120	Leist.v.ausländ.Untern. 16%/19% VSt.u.16%/19%USt.	A	E	AP_amount	9	5	\N	8	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
262	1772	Umsatzsteuer 7% innergem.Erwerb	A	I	AR_tax:IC_taxpart:IC_taxservice	0	\N	\N	6	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
263	1773	Umsatzsteuer 16% innergem.Erwerb	A	I	AR_tax:IC_taxpart:IC_taxservice	0	\N	\N	28	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
258	1572	Abziehbare Vorsteuer 7% innergem. Erwerb	A	E	AP_tax:IC_taxpart:IC_taxservice	0	\N	\N	27	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
260	1573	Abziehbare Vorsteuer 16% innergem. Erwerb	A	E	AP_tax:IC_taxpart:IC_taxservice	0	\N	\N	27	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
39	3425	Innergem. Erwerb 16%/19% VSt u. USt.	A	E	AP_amount:IC_cogs	0	4	\N	8	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
38	3420	Innergem. Erwerb 7% VSt u. USt.	A	E	AP_amount:IC_cogs	0	4	\N	8	t	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
194	1771	Umsatzsteuer 7%	A	I	AR_tax:IC_taxpart:IC_taxservice	2	\N	\N	6	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.18863	\N	\N
162	8735	Gewährte Skonti 16%/19% USt.	A	I	AR_paid	3	1	\N	1	f	2015-02-01 15:29:33.757477	2015-02-01 15:52:39.364595	\N	\N
\.


--
-- Data for Name: contacts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY contacts (cp_id, cp_cv_id, cp_title, cp_givenname, cp_name, cp_email, cp_phone1, cp_phone2, itime, mtime, cp_fax, cp_mobile1, cp_mobile2, cp_satphone, cp_satfax, cp_project, cp_privatphone, cp_privatemail, cp_abteilung, cp_gender, cp_street, cp_zipcode, cp_city, cp_birthday, cp_position, cp_homepage, cp_notes, cp_beziehung, cp_sonder, cp_stichwort1, cp_owener, cp_employee, cp_grafik, cp_country, cp_salutation) FROM stdin;
\.


--
-- Data for Name: contmasch; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY contmasch (mid, cid) FROM stdin;
\.


--
-- Data for Name: contract; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY contract (cid, contractnumber, template, bemerkung, customer_id, anfangdatum, betrag, endedatum) FROM stdin;
\.


--
-- Data for Name: crm; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY crm (id, uid, datum, version) FROM stdin;
87	0	2015-02-01 16:02:41.362647	1.9.0
88	890	2015-02-01 16:02:41.439045	2.0.2
\.


--
-- Data for Name: crmdefaults; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY crmdefaults (id, employee, key, val, grp, modify) FROM stdin;
89	890	ttpart	\N	mandant   	2015-02-01 16:29:26.03549
90	890	tttime	60	mandant   	2015-02-01 16:29:26.03549
91	890	ttround	15	mandant   	2015-02-01 16:29:26.03549
92	890	ttclearown	\N	mandant   	2015-02-01 16:29:26.03549
93	890	GEODB	\N	mandant   	2015-02-01 16:29:26.03549
94	890	BLZDB	\N	mandant   	2015-02-01 16:29:26.03549
95	890	CallDel	\N	mandant   	2015-02-01 16:29:26.03549
96	890	CallEdit	\N	mandant   	2015-02-01 16:29:26.03549
97	890	Expunge	\N	mandant   	2015-02-01 16:29:26.03549
98	890	MailFlag	Flagged	mandant   	2015-02-01 16:29:26.03549
99	890	logmail	t	mandant   	2015-02-01 16:29:26.03549
100	890	dir_group	users	mandant   	2015-02-01 16:29:26.03549
101	890	dir_mode	493	mandant   	2015-02-01 16:29:26.03549
102	890	sep_cust_vendor	t	mandant   	2015-02-01 16:29:26.03549
103	890	listLimit	500	mandant   	2015-02-01 16:29:26.03549
104	890	logfile	\N	mandant   	2015-02-01 16:29:26.03549
105	890	streetview_man	\N	mandant   	2015-02-01 16:29:26.03549
106	890	planspace_man	\N	mandant   	2015-02-01 16:29:26.03549
\.


--
-- Data for Name: crmemployee; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY crmemployee (manid, uid, key, val, typ) FROM stdin;
1	890	msrv	your_mail_server	t
1	890	postf		t
1	890	kennw	kivitendo	t
1	890	postf2	\N	t
1	890	mailsign	--Your Signature	t
1	890	email		t
1	890	mailuser	your_mail_login	t
1	890	port	143	i
1	890	proto	t	t
1	890	ssl	f	t
1	890	addr1		t
1	890	addr2		t
1	890	addr3		t
1	890	workphone		t
1	890	homephone		t
1	890	notes		t
1	890	abteilung		t
1	890	position		t
1	890	interv	60	i
1	890	pre	%	t
1	890	preon	t	b
1	890	vertreter		i
1	890	etikett	1	i
1	890	termbegin	8	i
1	890	termend	20	i
1	890	termseq	30	i
1	890	kdviewli	3	i
1	890	kdviewre	3	i
1	890	searchtab	1	i
1	890	icalart		t
1	890	icaldest		t
1	890	icalext		t
1	890	deleted	\N	b
1	890	streetview		t
1	890	planspace		t
1	890	streetview_default	t	b
1	890	theme	blue-style	t
1	890	smask	\N	t
1	890	helpmode	\N	b
1	890	listen_theme	\N	t
1	890	auftrag_button	t	b
1	890	angebot_button	t	b
1	890	rechnung_button	t	b
1	890	liefer_button	t	b
1	890	zeige_extra	t	b
1	890	zeige_lxcars	\N	b
1	890	zeige_karte	t	b
1	890	zeige_tools	t	b
1	890	zeige_etikett	t	b
1	890	zeige_bearbeiter	t	b
1	890	feature_ac	t	b
1	890	feature_ac_minlength	2	i
1	890	feature_ac_delay	100	i
1	890	feature_unique_name_plz	t	b
1	890	sql_error	\N	b
1	890	php_error	\N	b
1	890	external_mail	\N	b
1	890	zeige_dhl	\N	b
1	890	data_from_tel	\N	b
1	890	tinymce	t	b
1	890	mandsig	0	t
1	890	search_history	{"5":["891","Demo","C"]}	t
\.


--
-- Name: crmid; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('crmid', 106, true);


--
-- Data for Name: csv_import_profile_settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY csv_import_profile_settings (id, csv_import_profile_id, key, value) FROM stdin;
\.


--
-- Name: csv_import_profile_settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('csv_import_profile_settings_id_seq', 1, false);


--
-- Data for Name: csv_import_profiles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY csv_import_profiles (id, name, type, is_default, login) FROM stdin;
\.


--
-- Name: csv_import_profiles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('csv_import_profiles_id_seq', 1, false);


--
-- Data for Name: csv_import_report_rows; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY csv_import_report_rows (id, csv_import_report_id, col, "row", value) FROM stdin;
\.


--
-- Name: csv_import_report_rows_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('csv_import_report_rows_id_seq', 1, false);


--
-- Data for Name: csv_import_report_status; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY csv_import_report_status (id, csv_import_report_id, "row", type, value) FROM stdin;
\.


--
-- Name: csv_import_report_status_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('csv_import_report_status_id_seq', 1, false);


--
-- Data for Name: csv_import_reports; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY csv_import_reports (id, session_id, profile_id, type, file, numrows, numheaders) FROM stdin;
\.


--
-- Name: csv_import_reports_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('csv_import_reports_id_seq', 1, false);


--
-- Data for Name: currencies; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY currencies (id, name) FROM stdin;
1	EUR
\.


--
-- Name: currencies_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('currencies_id_seq', 1, true);


--
-- Data for Name: custmsg; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY custmsg (id, fid, prio, msg, uid, akt) FROM stdin;
\.


--
-- Data for Name: custom_variable_config_partsgroups; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY custom_variable_config_partsgroups (custom_variable_config_id, partsgroup_id, itime, mtime) FROM stdin;
\.


--
-- Data for Name: custom_variable_configs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY custom_variable_configs (id, name, description, type, module, default_value, options, searchable, includeable, included_by_default, sortkey, itime, mtime, flags) FROM stdin;
\.


--
-- Name: custom_variable_configs_id; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('custom_variable_configs_id', 1, false);


--
-- Data for Name: custom_variables; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY custom_variables (id, config_id, trans_id, bool_value, timestamp_value, text_value, number_value, itime, mtime, sub_module) FROM stdin;
\.


--
-- Name: custom_variables_id; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('custom_variables_id', 1, false);


--
-- Data for Name: custom_variables_validity; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY custom_variables_validity (id, config_id, trans_id, itime) FROM stdin;
\.


--
-- Data for Name: customer; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY customer (id, name, department_1, department_2, street, zipcode, city, country, contact, phone, fax, homepage, email, notes, discount, taxincluded, creditlimit, terms, customernumber, cc, bcc, business_id, taxnumber, account_number, bank_code, bank, language, itime, mtime, obsolete, username, user_password, salesman_id, c_vendor_id, klass, language_id, payment_id, taxzone_id, greeting, ustid, iban, bic, direct_debit, depositor, taxincluded_checked, mandator_id, mandate_date_of_signature, delivery_term_id, hourly_rate, currency_id, owener, employee, sw, branche, grafik, sonder, lead, leadsrc, bland, konzern, headcount) FROM stdin;
891	Demo	\N	\N	Demostraße 69	15345	Rehfelde	D	\N	03341-364419	0175-7880999	\N	info@lxcars.de	\N	\N	\N	0.00000	0	1	\N	\N	892	\N	\N	\N	\N	\N	2015-02-01 16:31:35.206804	2015-02-01 17:32:58.187812	f	\N	\N	\N	\N	0	\N	\N	4	Frau	\N	\N	\N	f	\N	\N	\N	\N	\N	\N	1	\N	890	\N	\N	\N	\N	\N	\N	39	\N	\N
\.


--
-- Data for Name: datev; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY datev (beraternr, beratername, mandantennr, dfvkz, datentraegernr, abrechnungsnr, itime, mtime, id) FROM stdin;
\.


--
-- Name: datev_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('datev_id_seq', 1, false);


--
-- Data for Name: defaults; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY defaults (inventory_accno_id, income_accno_id, expense_accno_id, fxgain_accno_id, fxloss_accno_id, invnumber, sonumber, weightunit, businessnumber, version, closedto, revtrans, ponumber, sqnumber, rfqnumber, customernumber, vendornumber, articlenumber, servicenumber, coa, itime, mtime, rmanumber, cnnumber, accounting_method, inventory_system, profit_determination, dunning_ar_amount_fee, dunning_ar_amount_interest, dunning_ar, pdonumber, sdonumber, ar_paid_accno_id, id, language_id, datev_check_on_sales_invoice, datev_check_on_purchase_invoice, datev_check_on_ar_transaction, datev_check_on_ap_transaction, datev_check_on_gl_transaction, payments_changeable, is_changeable, ir_changeable, ar_changeable, ap_changeable, gl_changeable, show_bestbefore, sales_order_show_delete, purchase_order_show_delete, sales_delivery_order_show_delete, purchase_delivery_order_show_delete, is_show_mark_as_paid, ir_show_mark_as_paid, ar_show_mark_as_paid, ap_show_mark_as_paid, warehouse_id, bin_id, company, address, taxnumber, co_ustid, duns, sepa_creditor_id, templates, max_future_booking_interval, webdav, webdav_documents, vertreter, parts_show_image, parts_listing_image, parts_image_css, normalize_vc_names, normalize_part_descriptions, assemblynumber, show_weight, transfer_default, transfer_default_use_master_default_bin, transfer_default_ignore_onhand, warehouse_id_ignore_onhand, bin_id_ignore_onhand, balance_startdate_method, currency_id, customer_hourly_rate, signature, requirement_spec_section_order_part_id, transfer_default_services, delivery_plan_show_value_of_goods, delivery_plan_calculate_transferred_do, global_bcc, customer_projects_only_in_sales, reqdate_interval, require_transaction_description_ps, allow_sales_invoice_from_sales_quotation, allow_sales_invoice_from_sales_order, allow_new_purchase_delivery_order, allow_new_purchase_invoice, disabled_price_sources, transport_cost_reminder_article_number_id, contnumber) FROM stdin;
\N	\N	\N	\N	\N	\N	\N	\N	\N	2.4.0.0	\N	f	\N	\N	\N	1	\N	\N	\N	Germany-DATEV-SKR03EU	2015-02-01 15:29:32.080305	\N	\N	\N	cash	periodic	income	\N	\N	\N	0	0	\N	1	\N	f	f	f	f	f	0	2	2	2	2	2	f	t	t	t	t	t	t	t	t	\N	\N	\N	\N	\N	\N	\N	\N	\N	360	f	f	f	t	f	border:0;float:left;max-width:250px;margin-top:20px:margin-right:10px;margin-left:10px;	t	t	\N	f	t	f	f	\N	\N	closedto	1	100.00	\N	\N	t	f	f		f	0	f	t	t	t	t	\N	\N	\N
\.


--
-- Name: defaults_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('defaults_id_seq', 1, true);


--
-- Data for Name: delivery_order_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY delivery_order_items (id, delivery_order_id, parts_id, description, qty, sellprice, discount, project_id, reqdate, serialnumber, ordnumber, transdate, cusordnumber, unit, base_qty, longdescription, lastcost, price_factor_id, price_factor, marge_price_factor, itime, mtime, pricegroup_id, "position", active_price_source, active_discount_source) FROM stdin;
\.


--
-- Name: delivery_order_items_id; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('delivery_order_items_id', 1, false);


--
-- Data for Name: delivery_order_items_stock; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY delivery_order_items_stock (id, delivery_order_item_id, qty, unit, warehouse_id, bin_id, chargenumber, itime, mtime, bestbefore) FROM stdin;
\.


--
-- Data for Name: delivery_orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY delivery_orders (id, donumber, ordnumber, transdate, vendor_id, customer_id, reqdate, shippingpoint, notes, intnotes, employee_id, closed, delivered, cusordnumber, oreqnumber, department_id, shipvia, cp_id, language_id, shipto_id, globalproject_id, salesman_id, transaction_description, is_sales, itime, mtime, taxzone_id, taxincluded, terms, delivery_term_id, currency_id) FROM stdin;
\.


--
-- Data for Name: delivery_terms; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY delivery_terms (id, description, description_long, sortkey, itime, mtime) FROM stdin;
\.


--
-- Data for Name: department; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY department (id, description, itime, mtime) FROM stdin;
\.


--
-- Data for Name: docfelder; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY docfelder (fid, docid, feldname, platzhalter, beschreibung, laenge, zeichen, "position") FROM stdin;
\.


--
-- Data for Name: documents; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY documents (filename, descript, datum, zeit, size, pfad, kunde, lock, employee, id) FROM stdin;
\.


--
-- Data for Name: documenttotc; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY documenttotc (id, telcall, documents) FROM stdin;
\.


--
-- Data for Name: docvorlage; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY docvorlage (docid, vorlage, beschreibung, file, applikation) FROM stdin;
\.


--
-- Data for Name: drafts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY drafts (id, module, submodule, description, itime, form, employee_id) FROM stdin;
\.


--
-- Data for Name: dunning; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY dunning (id, trans_id, dunning_id, dunning_level, transdate, duedate, fee, interest, dunning_config_id, itime, mtime, fee_interest_ar_id) FROM stdin;
\.


--
-- Data for Name: dunning_config; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY dunning_config (id, dunning_level, dunning_description, active, auto, email, terms, payment_terms, fee, interest_rate, email_body, email_subject, email_attachment, template, create_invoices_for_fees) FROM stdin;
\.


--
-- Data for Name: employee; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY employee (id, login, startdate, enddate, sales, itime, mtime, name, deleted, deleted_email, deleted_signature, deleted_tel, deleted_fax) FROM stdin;
890	demo	2015-02-01	\N	t	2015-02-01 15:52:41.513848	\N	Demo User	f	\N	\N	\N	\N
\.


--
-- Data for Name: event_category; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY event_category (id, label, color, cat_order) FROM stdin;
\.


--
-- Name: event_category_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('event_category_id_seq', 1, true);


--
-- Data for Name: events; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY events (id, title, duration, repeat, repeat_factor, repeat_quantity, repeat_end, description, location, uid, prio, category, visibility, "allDay", color, job, done, job_planned_end, cust_vend_pers) FROM stdin;
\.


--
-- Name: events_tmp_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('events_tmp_id_seq', 1, false);


--
-- Data for Name: exchangerate; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY exchangerate (transdate, buy, sell, itime, mtime, id, currency_id) FROM stdin;
\.


--
-- Name: exchangerate_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('exchangerate_id_seq', 1, false);


--
-- Data for Name: extra_felder; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY extra_felder (id, owner, tab, fkey, fval) FROM stdin;
\.


--
-- Name: extraid; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('extraid', 1, false);


--
-- Data for Name: finanzamt; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY finanzamt (fa_land_nr, fa_bufa_nr, fa_name, fa_strasse, fa_plz, fa_ort, fa_telefon, fa_fax, fa_plz_grosskunden, fa_plz_postfach, fa_postfach, fa_blz_1, fa_kontonummer_1, fa_bankbezeichnung_1, fa_blz_2, fa_kontonummer_2, fa_bankbezeichnung_2, fa_oeffnungszeiten, fa_email, fa_internet, id) FROM stdin;
10	1010	Saarlouis 	Gaswerkweg 25	66740	Saarlouis	06831/4490	06831/449397		66714	1440	59000000	59301502	BBK SAARBRUECKEN	59010066	7761668	POSTBANK SAARBRUECKEN	Mo,Di,Do 7.30-15.30, Mi 7.30-18,Fr 7.30-12			1
10	1020	Merzig 	Am Gaswerk	66663	Merzig	06861/7030	06861/703133		66653	100232	59000000	59301502	BBK SAARBRUECKEN	59010066	7761668	POSTBANK SAARBRUECKEN	Mo-Do 7.30-15.30,Mi bis 18.00,Fr 7.30-12.00			2
10	1030	Neunkirchen 	Uhlandstr.	66538	Neunkirchen	06821/1090	06821/109275		66512	1234	59000000	59001508	BBK SAARBRUECKEN	59010066	2988669	POSTBANK SAARBRUECKEN	Mo-Do 7.30-15.30,Mi bis 18.00,Fr 07.30-12.00			3
10	1040	Saarbrücken Am Stadtgr 	Am Stadtgraben 2-4	66111	Saarbrücken	0681/30000	0681/3000329		66009	100952	59000000	59001502	BBK SAARBRUECKEN	59010066	7766663	POSTBANK SAARBRUECKEN	Mo,Di,Do 7.30-15.30, Mi 7.30-18,Fr 7.30-12			4
10	1055	Saarbrücken MainzerStr 	Mainzer Str.109-111	66121	Saarbrücken	0681/30000	0681/3000762		66009	100944	59000000	59001502	BBK SAARBRUECKEN	59010066	7766663	POSTBANK SAARBRUECKEN	Mo,Mi,Fr 8.30-12.00, zus. Mi 13.30 - 15.30			5
10	1060	St. Wendel 	Marienstr. 27	66606	St. Wendel	06851/8040	06851/804189		66592	1240	59000000	59001508	BBK SAARBRUECKEN	59010066	2988669	POSTBANK SAARBRUECKEN	Mo-Do 7.30-15.30,Mi bis 18.00,Fr 07.30-12.00			6
10	1070	Sulzbach 	Vopeliusstr. 8	66280	Sulzbach	06897/9082-0	06897/9082110		66272	1164	59000000	59001502	BBK SAARBRUECKEN	59010066	7766663	POSTBANK SAARBRUECKEN	Mo,Mi,Fr 08.30-12.00, zus. Mi 13.30-18.00			7
10	1075	Homburg 	Schillerstr. 15	66424	Homburg	06841/6970	06841/697199		66406	1551	59000000	59001508	BBK SAARBRUECKEN	59010066	2988669	POSTBANK SAARBRUECKEN	Mo-Do 7.30-15.30,Mi bis 18.00,Fr 07.30-12.00			8
10	1085	St. Ingbert 	Rentamtstr. 39	66386	St. Ingbert	06894/984-01	06894/984159		66364	1420	59000000	59001508	BBK SAARBRUECKEN	59010066	2988669	POSTBANK SAARBRUECKEN	Mo-Do 7.30-15.30,Mi bis 18.00,Fr 7.30-12.00			9
10	1090	Völklingen 	Marktstr.	66333	Völklingen	06898/20301	06898/203133		66304	101440	59000000	59001502	BBK SAARBRUECKEN	59010066	7766663	POSTBANK SAARBRUECKEN	Mo-Do 7.30-15.30,Mi bis 18.00,Fr 07.30-12.00			10
11	1113	Berlin Charlottenburg	Bismarckstraße 48	10627	Berlin	030 9024-13-0	030 9024-13-900				10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	facharlottenburg@berlin.de	http://www.berlin.de/ofd	11
11	1114	Berlin Kreuzberg	Mehringdamm 22	10961	Berlin	030 9024-14-0	030 9024-14-900	10958			10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fakreuzberg@berlin.de	http://www.berlin.de/oberfinanzdirektion	12
11	1115	Berlin Neukölln																faneukoelln@berlin.de		13
11	1116	Berlin Neukölln	Thiemannstr. 1	12059	Berlin	030 9024-16-0	030 9024-16-900				10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	faneukoelln@berlin.de	http://www.berlin.de/oberfinanzdirektion	14
11	1117	Berlin Reinickendorf	Eichborndamm 208	13403	Berlin	030 9024-17-0	030 9024-17-900	13400			10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fareinickendorf@berlin.de	http://www.berlin.de/oberfinanzdirektion	15
11	1118	Berlin Schöneberg	Bülowstraße 85/88	10783	Berlin	030/9024-18-0	030/9024-18-900				10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Montag und Freitag: 8:00 - 13:00 Uhr Donnerstag: 11:00 - 18:00 Uhr	faschoeneberg@berlin.de	http://www.berlin.de/oberfinanzdirektion	16
11	1119	Berlin Spandau	Nonnendammallee 15-21	13599	Berlin	030/9024-19-0	030/9024-19-900				10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	faspandau@berlin.de	http://www.berlin.de/oberfinanzdirektion	17
11	1120	Berlin Steglitz	Schloßstr. 58/59	12165	Berlin	030/9024-20-0	030/9024-20-900				10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fasteglitz@berlin.de	http://www.berlin.de/oberfinanzdirektion	18
11	1121	Berlin Tempelhof	Tempelhofer Damm 234/236	12099	Berlin	030 9024-21-0	030 9024-21-900	12096			10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fatempelhof@berlin.de	http://www.berlin.de/oberfinanzdirektion	19
11	1123	Berlin Wedding	Osloer Straße 37	13359	Berlin	030 9024-23-0	030 9024-23-900				10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fawedding@berlin.de	http://www.berlin.de/oberfinanzdirektion	20
11	1124	Berlin Wilmersdorf	Blissestr. 5	10713	Berlin	030/9024-24-0	030/9024-24-900	10702			10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fawilmersdorf@berlin.de	http://www.berlin.de/oberfinanzdirektion	21
11	1125	Berlin Zehlendorf	Martin-Buber-Str. 20/21	14163	Berlin	030 9024-25-0	030 9024-25-900	14160			10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fazehlendorf@berlin.de	http://www.berlin.de/oberfinanzdirektion	22
11	1127	Berlin für Körperschaften I	Gerichtstr. 27	13347	Berlin	030 9024-27-0	030 9024-27-900				10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fakoerperschaften1@berlin.de	http://www.berlin.de/oberfinanzdirektion	23
11	1128	Berlin Pankow/Weißennsee - nur KFZ-Steuer -	Berliner Straße 32	13089	Berlin	030/4704-0	030/94704-1777	13083			10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	pankow.weissensee@berlin.de	http://www.berlin.de/oberfinanzdirektion	24
11	1129	Berlin für Körperschaften III	Volkmarstr. 13	12099	Berlin	030/70102-0	030/70102-100		12068	420844	10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fakoeperschaften3@berlin.de	http://www.berlin.de/oberfinanzdirektion	25
11	1130	Berlin für Körperschaften IV	Magdalenenstr. 25	10365	Berlin	030 9024-30-0	030 9024-30-900				10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fakoeperschaften4@berlin.de	http://www.berlin.de/oberfinanzdirektion	26
11	1131	Berlin Friedrichsh./Prenzb.	Pappelallee 78/79	10437	Berlin	030 9024-28-0	030 9024-28-900	10431			10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fafriedrichshain.prenzlauerberg@berlin.de	http://www.berlin.de/oberfinanzdirektion	27
11	1132	Berlin Lichtenb./Hohenschh.	Josef-Orlopp-Str. 62	10365	Berlin	030/5501-0	030/55012222	10358			10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	falichtenberg.hohenschoenhausen@berlin.de	http://www.berlin.de/oberfinanzdirektion	28
11	1133	Berlin Hellersdorf/Marzahn	Allee der Kosmonauten 29	12681	Berlin	030 9024-26-0	030 9024-26-900	12677			10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fahellersdorf.marzahn@berlin.de	http://www.berlin.de/oberfinanzdirektion	29
11	1134	Berlin Mitte/Tiergarten	Neue Jakobstr. 6-7	10179	Berlin	030 9024-22-0	030 9024-22-900				10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	famitte.tiergarten@berlin.de	http://www.berlin.de/oberfinanzdirektion	30
11	1135	Berlin Pankow/Weißensee	Berliner Straße 32	13089	Berlin	030/4704-0	030/47041777	13083			10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	pankow.weissensee@berlin.de	http://www.berlin.de/oberfinanzdirektion	31
11	1136	Berlin Treptow/Köpenick	Seelenbinderstr. 99	12555	Berlin	030 9024-12-0	030 9024-12-900				10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fatreptow.koepenick@berlin.de	http://www.berlin.de/oberfinanzdirektion	32
11	1137	Berlin für Körperschaften II	Magdalenenstr. 25	10365	Berlin	030 9024-29-0	030 9024-29-900				10010010	691555100	POSTBANK BERLIN	10050000	6600046463	LBB GZ - BERLINER SPARKASSE	Mo und Fr 8:00 - 13:00, Do 11:00 - 18:00 Uhr und nach Vereinbarung	fakoeperschaften2@berlin.de	http://www.berlin.de/oberfinanzdirektion	33
11	1138	Berlin für Fahndung und Strafsachen	Colditzstr. 41	12099	Berlin	030/70102-777	030/70102-700										Mo - Mi 10:00 - 14:00, Do 10:00 - 18:00, Fr 10:00 - 14:00 Uhr	fafahndung.strafsachen@berlin.de	http://www.berlin.de/oberfinanzdirektion	34
1	2111	Bad Segeberg 	Theodor-Storm-Str. 4-10	23795	Bad Segeberg	04551 54-0	04551 54-303	23792			23000000	23001502	BBK LUEBECK	23051030	744	KR SPK SUEDHOLSTEIN BAD SEG	0830-1200 MO, DI, DO, FR, 1330-1630 DO			35
1	2112	Eckernförde 	Bergstr. 50	24340	Eckernförde	04351 756-0	04351 83379		24331	1180	21000000	21001500	BBK KIEL	21092023	11511260	ECKERNFOERDER BANK VRB	0800-1200 MO-FR			36
1	2113	Elmshorn 	Friedensallee 7-9	25335	Elmshorn	04121 481-0	04121 481-460	25333			22200000	22201502	BBK KIEL EH ITZEHOE				0800-1200 MO-FR			37
1	2114	Eutin 	Robert-Schade-Str. 22	23701	Eutin	04521 704-0	04521 704-406		23691	160	23000000	23001505	BBK LUEBECK	21352240	4283	SPK OSTHOLSTEIN EUTIN	0830-1200 MO-FR, Nebenstelle Janusstr. 5 am Mo., Di, Do und Fr. 0830-1200, Do. 1330-1700			38
1	2115	Flensburg 	Duburger Str. 58-64	24939	Flensburg	0461 813-0	0461 813-254		24905	1552	21500000	21501500	BBK FLENSBURG				0800-1200 MO-FR			39
1	2116	Heide 	Ernst-Mohr-Str. 34	25746	Heide	0481 92-1	0481 92-690	25734			21500000	21701502	BBK FLENSBURG	22250020	60000123	SPK WESTHOLSTEIN	0800-1200 MO, DI, DO, FR, 1400-1700 DO			40
1	2117	Husum 	Herzog-Adolf-Str. 18	25813	Husum	04841 8949-0	04841 8949-200		25802	1230	21500000	21701500	BBK FLENSBURG				0800-1200 MO-FR			41
1	2118	Itzehoe 	Fehrsstr. 5	25524	Itzehoe	04821 66-0	04821 661-499		25503	1344	22200000	22201500	BBK KIEL EH ITZEHOE				0800-1200 MO, DI, DO, FR, 1400-1730 DO			42
1	2119	Kiel-Nord 	Holtenauer Str. 183	24118	Kiel	0431 8819-0	0431 8819-1200	24094			21000000	21001501	BBK KIEL	21050000	52001500	HSH NORDBANK KIEL	0800-1200 MO-FR 1430-1600 DI			43
1	2120	Kiel-Süd 	Hopfenstr. 2a	24114	Kiel	0431 602-0	0431 602-1009	24095			21000000	21001502	BBK KIEL	21050000	52001510	HSH NORDBANK KIEL	0800-1200 MO, DI, DO, FR, 1430-1730 DI			44
1	2121	Leck 	Eesacker Str. 11 a	25917	Leck	04662 85-0	04662 85-266		25912	1240	21700000	21701501	BBK FLENSBURG EH HUSUM	21750000	80003569	NORD-OSTSEE SPK SCHLESWIG	0800-1200 MO-FR			45
1	2122	Lübeck 	Possehlstr. 4	23560	Lübeck	0451 132-0	0451 132-501	23540			23000000	23001500	BBK LUEBECK	21050000	7052000200	HSH NORDBANK KIEL	0730-1300 MO+DI 0730-1700 Do 0730-1200 Fr			46
1	2123	Meldorf 	Jungfernstieg 1	25704	Meldorf	04832 87-0	04832 87-2508		25697	850	21500000	21701503	BBK FLENSBURG	21851830	106747	VERB SPK MELDORF	0800-1200 MO, DI, DO, FR, 1400-1700 MO			47
1	2124	Neumünster 	Bahnhofstr. 9	24534	Neumünster	04321 496 0	04321 496-189	24531			21000000	21001507	BBK KIEL				0800-1200 MO-MI, FR 1400-1700 DO			48
1	2125	Oldenburg 	Lankenstr. 1	23758	Oldenburg	04361 497-0	04361 497-125		23751	1155	23000000	23001504	BBK LUEBECK	21352240	51000396	SPK OSTHOLSTEIN EUTIN	0900-1200 MO-FR 1400-1600 MI			49
1	2126	Plön 	Fünf-Seen-Allee 1	24306	Plön	04522 506-0	04522 506-2149		24301	108	21000000	21001503	BBK KIEL	21051580	2600	SPK KREIS PLOEN	0800-1200 MO, Di, Do, Fr, 1400-1700 Di			50
1	2127	Ratzeburg 	Bahnhofsallee 20	23909	Ratzeburg	04541 882-01	04541 882-200	23903			23000000	23001503	BBK LUEBECK	23052750	100188	KR SPK LAUENBURG RATZEBURG	0830-1230 MO, DI, DO, FR, 1430-1730 DO			51
1	2128	Rendsburg 	Ritterstr. 10	24768	Rendsburg	04331 598-0	04331 598-2770		24752	640	21000000	21001504	BBK KIEL	21450000	1113	SPK MITTELHOLSTEIN RENDSBG	0730-1200 MO-FR			52
1	2129	Schleswig 	Suadicanistr. 26-28	24837	Schleswig	04621 805-0	04621 805-290		24821	1180	21500000	21501501	BBK FLENSBURG	21690020	91111	VOLKSBANK RAIFFEISENBANK	0800-1200 MO, DI, DO, FR, 1430-1700 DO			53
1	2130	Stormarn 	Berliner Ring 25	23843	Bad Oldesloe	04531 507-0	04531 507-399	23840			23000000	23001501	BBK LUEBECK	23051610	20503	SPK BAD OLDESLOE	0830-1200 MO-FR			54
1	2131	Pinneberg 	Friedrich-Ebert-Str. 29	25421	Pinneberg	04101 5472-0	04101 5472-680		25404	1451	22200000	22201503	BBK KIEL EH ITZEHOE				0800-1200 MO-FR			55
1	2132	Bad Segeberg / Außenst.Norderstedt	Europaallee 22	22850	Norderstedt	040 523068-0	040 523068-70				23000000	23001502	BBK LUEBECK	23051030	744	KR SPK SUEDHOLSTEIN BAD SEG	0830-1200 MO, DI, DO, FR, 1330-1630 DO			56
2	2201	Hamburg Steuerkasse	Steinstraße 10	20095	Hamburg	040/42853-03	040/42853-2159		20041	106026	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAfuerSteuererhebung@finanzamt.hamburg.de		57
2	2210	Hamburg f.VerkSt.u.Grundbes-10	Gorch-Fock-Wall 11	20355	Hamburg	040/42843-60	040/42843-6199		20306	301721	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAfuerVerkehrsteuern@finanzamt.hamburg.de		63
2	2216	Hamburg f.VerkSt.u.Grundbes-16	Gorch-Fock-Wall 11	20355	Hamburg	040/42843-60	040/42843-6199		20306	301721	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAfuerVerkehrsteuern@finanzamt.hamburg.de		65
2	2217	Hamburg-Mitte-Altstadt 17 	Wendenstraße 35 b	20097	Hamburg	040/42853-06	040/42853-6671		20503	261338	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAHamburgMitteAltstadt@finanzamt.hamburg.de		66
2	2220	Hamburg f.VerkSt.u.Grundbes-20	Gorch-Fock-Wall 11	20355	Hamburg	040/42843-60	040/42843-6599		20306	301721	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAfuerVerkehrsteuern@finanzamt.hamburg.de		67
2	2224	Hamburg-Mitte-Altstadt 	Wendenstr. 35 b	20097	Hamburg	040/42853-06	040/42853-6671		20503	261338	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAHamburgMitteAltstadt@finanzamt.hamburg.de		69
2	2225	Hamburg-Neustadt-St.Pauli 	Steinstraße 10	20095	Hamburg	040/42853-02	040/42853-2106		20015	102246	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAHamburgNeustadt@finanzamt.hamburg.de		70
2	2227	Hamburg für Großunternehmen	Amsinckstr. 40	20097	Hamburg	040/42853-05	040/42853-5559		20015	102205	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAfuerGroßunternehmen@finanzamt.hamburg.de		72
2	2228	Hamburg Neust.-St.Pauli-28	Steinstr. 10	20095	Hamburg	040/42853-3589	040/42853-2106		20015	102246	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAHamburgNeustadt@finanzamt.hamburg.de		73
2	2230	Hamburg f.Verkehrst.u.Grundbes	Gorch-Fock-Wall 11	20355	Hamburg	040/42843-60	040/42843-6799		20306	301721	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAfuerVerkehrsteuern@finanzamt.hamburg.de		74
2	2235	Hamburg f.VerkSt.u.Grundbes-35	Gorch-Fock-Wall 11	20355	Hamburg	040/42843-60	040/42843-6199		20306	301721	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAfuerVerkehrsteuern@finanzamt.hamburg.de		75
3	2311	Alfeld (Leine) 	Ravenstr.10	31061	Alfeld	05181/7050	05181/705240		31042	1244	25000000	25901505	BBK HANNOVER	25950130	10011102	KR SPK HILDESHEIM	Mo. - Fr. 8.30 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-alf.niedersachsen.de	www.ofd.niedersachsen.de	79
3	2312	Bad Gandersheim 	Alte Gasse 24	37581	Bad Gandersheim	05382/760	(05382) 76-213 + 204		37575	1180	26000000	26001501	BBK GOETTINGEN	25050000	22801005	NORD LB HANNOVER	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-gan.niedersachsen.de	www.ofd.niedersachsen.de	80
3	2313	Braunschweig-Altewiekring 	Altewiekring 20	38102	Braunschweig	0531/7050	0531/705309		38022	3229	27000000	27001501	BBK BRAUNSCHWEIG	25050000	2498020	NORD LB HANNOVER	Mo. - Fr. 8.00 - 12.00 Uhr, Mo. 14.00 - 17.00 Uhr	Poststelle@fa-bs-a.niedersachsen.de	www.ofd.niedersachsen.de	81
3	2314	Braunschweig-Wilhelmstr. 	Wilhelmstr. 4	38100	Braunschweig	0531/4890	0531/489224		38022	3249	27000000	27001502	BBK BRAUNSCHWEIG	25050000	811422	NORD LB HANNOVER	Mo. - Fr. 8.00 - 12.00 Uhr, Mo. 14.00 - 17.00 Uhr	Poststelle@fa-bs-w.niedersachsen.de	www.ofd.niedersachsen.de	82
3	2315	Buchholz in der Nordheide 	Bgm.-A.-Meyer-Str. 5	21244	Buchholz	04181/2030	(04181) 203-4444		21232	1262	20000000	20001520	BBK HAMBURG	20750000	3005063	SPK HARBURG-BUXTEHUDE	Mo. - Fr. 8.00 - 12.00 Uhr , Do. 14.00 - 17.00 Uhr	Poststelle@fa-buc.niedersachsen.de	www.ofd.niedersachsen.de	83
3	2316	Burgdorf 	V.d.Hannov. Tor 30	31303	Burgdorf	05136/8060	05136/806144	31300			25000000	25001515	BBK HANNOVER	25050180	1040400010	SPARKASSE HANNOVER	Mo. - Fr. 8.30 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-bu.niedersachsen.de	www.ofd.niedersachsen.de	84
3	2317	Celle 	Sägemühlenstr. 5	29221	Celle	(05141) 915-0	05141/915666		29201	1107	25000000	25701511	BBK HANNOVER	25750001	59	SPARKASSE CELLE	Mo. - Fr. 8.00 - 12.00 Uhr , Do. 14.00 - 17.00 Uhr	Poststelle@fa-ce.niedersachsen.de	www.ofd.niedersachsen.de	85
3	2318	Cuxhaven 	Poststr. 81	27474	Cuxhaven	(04721) 563-0	04721/563313		27452	280	29000000	24101501	BBK BREMEN	24150001	100503	ST SPK CUXHAVEN	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-cux.niedersachsen.de	www.ofd.niedersachsen.de	86
3	2319	Gifhorn 	Braunschw. Str. 6-8	38518	Gifhorn	05371/8000	05371/800241		38516	1249	27000000	27001503	BBK BRAUNSCHWEIG	26951311	11009958	SPK GIFHORN-WOLFSBURG	Mo. - Fr. 8.00 - 12.00 Uhr, Do. 14.00 - 17.00	Poststelle@fa-gf.niedersachsen.de	www.ofd.niedersachsen.de	87
3	2320	Göttingen 	Godehardstr. 6	37073	Göttingen	0551/4070	0551/407449	37070			26000000	26001500	BBK GOETTINGEN	26050001	91	SPARKASSE GOETTINGEN	Servicecenter: Mo., Di., Mi. und Fr. 8.00 - 12.00 u. Do. 8.00 - 17.00 Uhr,	Poststelle@fa-goe.niedersachsen.de	www.ofd.niedersachsen.de	88
3	2321	Goslar 	Wachtelpforte 40	38644	Goslar	05321/5590	05321/559200		38604	1440	27000000	27001505	BBK BRAUNSCHWEIG	26850001	2220	SPARKASSE GOSLAR/HARZ	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-gs.niedersachsen.de	www.ofd.niedersachsen.de	89
2	2242	Hamburg-Am Tierpark 	Hugh-Greene-Weg 6	22529	Hamburg				22520		20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAHamburgAmTierpark@finanzamt.hamburg.de		77
3	2322	Hameln 	Süntelstraße 2	31785	Hameln	05151/2040	05151/204200		31763	101325	25000000	25401511	BBK HANNOVER	25450001	430	ST SPK HAMELN	Mo. - Fr. 8.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-hm.niedersachsen.de	www.ofd.niedersachsen.de	90
3	2323	Hannover-Land I 	Göttinger Chaus. 83A	30459	Hannover	(0511) 419-1	0511/4192269		30423	910320	25000000	25001512	BBK HANNOVER	25050000	101342434	NORD LB HANNOVER	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-h-l1.niedersachsen.de	www.ofd.niedersachsen.de	91
3	2324	Hannover-Mitte 	Lavesallee 10	30169	Hannover	0511/16750	0511/1675277		30001	143	25000000	25001516	BBK HANNOVER	25050000	101341816	NORD LB HANNOVER	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhrund nach Vereinbarung	Poststelle@fa-h-mi.niedersachsen.de	www.ofd.niedersachsen.de	92
3	2325	Hannover-Nord 	Vahrenwalder Str.206	30165	Hannover	0511/67900	(0511) 6790-6090		30001	167	25000000	25001514	BBK HANNOVER	25050000	101342426	NORD LB HANNOVER	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-h-no.niedersachsen.de	www.ofd.niedersachsen.de	93
3	2326	Hannover-Süd 	Göttinger Chaus. 83B	30459	Hannover	0511/4191	0511/4192575		30423	910355	25000000	25001517	BBK HANNOVER	25050000	101342400	NORD LB HANNOVER	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-h-su.niedersachsen.de	www.ofd.niedersachsen.de	94
3	2327	Hannover-Land II 	Vahrenwalder Str.208	30165	Hannover	0511/67900	(0511) 6790-6633		30001	165	25000000	25001520	BBK HANNOVER	25050000	101342517	NORD LB HANNOVER	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-h-l2.niedersachsen.de	www.ofd.niedersachsen.de	95
3	2328	Helmstedt 	Ernst-Koch-Str.3	38350	Helmstedt	05351/1220	(05351) 122-299		38333	1320	27000000	27101500	BBK BRAUNSCHWEIG	25050000	5801006	NORD LB HANNOVER	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-he.niedersachsen.de	www.ofd.niedersachsen.de	96
3	2329	Herzberg am Harz 	Sieberstr. 1	37412	Herzberg	05521/8570	05521/857220		37401	1153	26000000	26001502	BBK GOETTINGEN	26351015	1229327	SPARKASSE IM KREIS OSTERODE	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-hz.niedersachsen.de	www.ofd.niedersachsen.de	97
3	2330	Hildesheim 	Kaiserstrasse 47	31134	Hildesheim	05121/3020	05121/302480		31104	100455	25000000	25901500	BBK HANNOVER	25950130	5555	KR SPK HILDESHEIM	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-hi.niedersachsen.de	www.ofd.niedersachsen.de	98
3	2331	Holzminden 	Ernst-August-Str.30	37603	Holzminden	05531/1220	05531/122100		37601	1251	25000000	25401512	BBK HANNOVER	25050000	27811140	NORD LB HANNOVER	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-hol.niedersachsen.de	www.ofd.niedersachsen.de	99
3	2332	Lüchow 	Mittelstr.5	29439	Lüchow	(05841) 963-0	05841/963170		29431	1144	24000000	25801503	BBK LUENEBURG	25851335	2080000	KR SPK LUECHOW-DANNENBERG	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-luw.niedersachsen.de	www.ofd.niedersachsen.de	100
3	2333	Lüneburg 	Am Alt. Eisenwerk 4a	21339	Lüneburg	04131/3050	04131/305915	21332			24000000	24001500	BBK LUENEBURG	24050110	18	SPK LUENEBURG	Mo. - Fr. 8.00-12.00 Uhr, Do. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-lg.niedersachsen.de	www.ofd.niedersachsen.de	101
3	2334	Nienburg/Weser 	Schloßplatz 10	31582	Nienburg	05021/8011	05021/801300		31580	2000	25000000	25601500	BBK HANNOVER	25650106	302224	SPARKASSE NIENBURG	Mo. - Fr. 7.30 - 12.00 Uhr und nach Vereinbarung, zusätzl. Arbeitnehmerbereich: Do. 14 -	Poststelle@fa-ni.niedersachsen.de	www.ofd.niedersachsen.de	102
3	2335	Northeim 	Graf-Otto-Str. 31	37154	Northeim	05551/7040	05551/704221		37142	1261	26000000	26201500	BBK GOETTINGEN	26250001	208	KR SPK NORTHEIM	Mo. - Fr. 8.30 - 12.30 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-nom.niedersachsen.de	www.ofd.niedersachsen.de	103
3	2336	Osterholz-Scharmbeck 	Pappstraße 2	27711	Osterholz-Scharmbeck	04791/3020	04791/302101		27701	1120	29000000	29001523	BBK BREMEN	29152300	202622	KR SPK OSTERHOLZ	Mo. - Fr. 8.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-ohz.niedersachsen.de	www.ofd.niedersachsen.de	104
3	2338	Peine 	Duttenstedt.Str. 106	31224	Peine	05171/4070	05171/407199	31221			27000000	27001507	BBK BRAUNSCHWEIG	25250001	75003210	KR SPK PEINE	Mo. - Mi. Fr. 9.00 - 12.00, Do. 13.30 - 16.00 UhrDo. (Infothek) 13.30 -	Poststelle@fa-pe.niedersachsen.de	www.ofd.niedersachsen.de	105
3	2340	Rotenburg (Wümme) 	Hoffeldstr. 5	27356	Rotenburg	04261/740	04261/74108		27342	1260	29000000	29001522	BBK BREMEN	24151235	26106377	SPK ROTENBURG-BREMERVOERDE	Mo. - Mi., Fr. 8.00 - 12.00 Uhr, Do. 8.00 - 17.30	Poststelle@fa-row.niedersachsen.de	www.ofd.niedersachsen.de	106
3	2341	Soltau 	Rühberg 16 - 20	29614	Soltau	05191/8070	05191/807144		29602	1243	24000000	25801502	BBK LUENEBURG	25851660	100016	KR SPK SOLTAU	Mo. - Fr. 8.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-sol.niedersachsen.de	www.ofd.niedersachsen.de	107
3	2342	Hannover-Land I Außenstelle Springe	Bahnhofstr. 28	31832	Springe	05041/7730	05041/77363		31814	100255	25000000	25001512	BBK HANNOVER	25050180	3001000037	SPARKASSE HANNOVER	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-ast-spr.niedersachsen.de	www.ofd.niedersachsen.de	108
3	2343	Stade 	Harburger Str. 113	21680	Stade	(04141) 536-0	(04141) 536-499	21677			24000000	24001560	BBK LUENEBURG	24151005	42507	SPK STADE-ALTES LAND	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-std.niedersachsen.de	www.ofd.niedersachsen.de	109
3	2344	Stadthagen 	Schloß	31655	Stadthagen	05721/7050	05721/705250	31653			49000000	49001502	BBK MINDEN, WESTF	25551480	470140401	SPARKASSE SCHAUMBURG	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-shg.niedersachsen.de	www.ofd.niedersachsen.de	110
3	2345	Sulingen 	Hindenburgstr. 16	27232	Sulingen	04271/870	04271/87289		27226	1520	29000000	29001516	BBK BREMEN	25651325	30101430	KR SPK DIEPHOLZ	Mo., Mi., Do. und Fr. 8.00 - 12.00 Uhr, Di. 8.00 - 17.00 Uhr	Poststelle@fa-su.niedersachsen.de	www.ofd.niedersachsen.de	111
3	2346	Syke 	Bürgerm.-Mävers-Str. 15	28857	Syke	04242/1620	04242/162423		28845	1164	29000000	29001515	BBK BREMEN	29151700	1110044557	KREISSPARKASSE SYKE	Mo. - Fr. 8.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-sy.niedersachsen.de	www.ofd.niedersachsen.de	112
3	2347	Uelzen 	Am Königsberg 3	29525	Uelzen	0581/8030	0581/803404		29504	1462	24000000	25801501	BBK LUENEBURG	25850110	26	SPARKASSE UELZEN	Mo. - Fr. 8.30 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-ue.niedersachsen.de	www.ofd.niedersachsen.de	113
3	2348	Verden (Aller) 	Bremer Straße 4	27283	Verden	04231/9190	04231/919310		27263	1340	29000000	29001517	BBK BREMEN	29152670	10000776	KR SPK VERDEN	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-ver.niedersachsen.de	www.ofd.niedersachsen.de	114
3	2349	Wesermünde 	Borriesstr. 50	27570	Bremerhaven	0471/1830	0471/183119		27503	100369	29000000	29201501	BBK BREMEN	29250150	100103200	KR SPK WESERMUENDE-HADELN	Mo. - Fr. 8.30 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-wem.niedersachsen.de	www.ofd.niedersachsen.de	115
3	2350	Winsen (Luhe) 	Von-Somnitz-Ring 6	21423	Winsen	04171/6560	(04171) 656-115		21413	1329	24000000	24001550	BBK LUENEBURG	20750000	7051519	SPK HARBURG-BUXTEHUDE	Mo. - Fr. 8.00 - 12.00 Uhr, Do. 14.00 - 18.00 Uhr und nach Vereinbarung	Poststelle@fa-wl.niedersachsen.de	www.ofd.niedersachsen.de	116
3	2351	Wolfenbüttel 	Jägerstr. 19	38304	Wolfenbüttel	05331/8030	(05331) 803-113/266 	38299			27000000	27001504	BBK BRAUNSCHWEIG	25050000	9801002	NORD LB HANNOVER	Mo. - Fr. 8.00 - 12.00 Uhr, Mi. 14.00 - 17.00 Uhr	Poststelle@fa-wf.niedersachsen.de	www.ofd.niedersachsen.de	117
3	2352	Zeven 	Kastanienweg 1	27404	Zeven	04281/7530	04281/753290		27392	1259	29000000	29201503	BBK BREMEN	24151235	404350	SPK ROTENBURG-BREMERVOERDE	Mo. - Fr. 8.30 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-zev.niedersachsen.de	www.ofd.niedersachsen.de	118
3	2353	Papenburg 	Große Straße 32	26871	Aschendorf	04962/5030	04962/503222		26883	2264	28000000	28501512	BBK OLDENBURG (OLDB)	26650001	1020007	SPK EMSLAND	Mo. - Fr. 9.00 - 12.00 Uhr, Mi. 14.00 - 17.00 Uhr	Poststelle@fa-pap.niedersachsen.de	www.ofd.niedersachsen.de	119
3	2354	Aurich 	Hasseburger Str. 3	26603	Aurich	04941/1750	04941/175152		26582	1260	28000000	28501514	BBK OLDENBURG (OLDB)	28350000	90001	SPK AURICH-NORDEN	Mo. - Fr. 8.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-aur.niedersachsen.de	www.ofd.niedersachsen.de	120
3	2355	Bad Bentheim 	Heinrich-Böll-Str. 2	48455	Bad Bentheim	05922/970-0	05922/970-2000		48443	1262	26500000	26601501	BBK OSNABRUECK	26750001	1000066	KR SPK NORDHORN	Mo. - Fr. 9.00 - 12.00 Uhr, Do 14.00 - 15.30 Uhr	Poststelle@fa-ben.niedersachsen.de	www.ofd.niedersachsen.de	121
3	2356	Cloppenburg 	Bahnhofstr. 57	49661	Cloppenburg	04471/8870	04471/887477		49646	1680	28000000	28001501	BBK OLDENBURG (OLDB)	28050100	80402100	LANDESSPARKASSE OLDENBURG	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-clp.niedersachsen.de	www.ofd.niedersachsen.de	122
3	2357	Delmenhorst 	Fr.-Ebert-Allee 15	27749	Delmenhorst	04221/1530	04221/153126	27747			29000000	29001521	BBK BREMEN	28050100	30475669	LANDESSPARKASSE OLDENBURG	Mo. - Fr. 9.00 - 12.00 Uhr, Di. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-del.niedersachsen.de	www.ofd.niedersachsen.de	123
3	2358	Emden 	Ringstr. 5	26721	Emden	(04921) 934-0	(04921) 934-499		26695	1553	28000000	28401500	BBK OLDENBURG (OLDB)	28450000	26	SPARKASSE EMDEN	Mo. - Fr. 8.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-emd.niedersachsen.de	www.ofd.niedersachsen.de	124
3	2360	Leer (Ostfriesland) 	Edzardstr. 12/16	26789	Leer	(0491) 9870-0	0491/9870209	26787			28000000	28501511	BBK OLDENBURG (OLDB)	28550000	849000	SPARKASSE LEER-WEENER	Mo. - Fr. 8.00 - 12.00 Uhr, nur Infothek: Mo., Do. 14.00 - 17.30 Uhr	Poststelle@fa-ler.niedersachsen.de	www.ofd.niedersachsen.de	125
3	2361	Lingen (Ems) 	Mühlentorstr. 14	49808	Lingen	0591/91490	0591/9149468		49784	1440	26500000	26601500	BBK OSNABRUECK	26650001	2402	SPK EMSLAND	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr und nach Vereinbarung	Poststelle@fa-lin.niedersachsen.de	www.ofd.niedersachsen.de	126
3	2362	Norden 	Mühlenweg 20	26506	Norden	04931/1880	04931/188196		26493	100360	28000000	28501515	BBK OLDENBURG (OLDB)	28350000	1115	SPK AURICH-NORDEN	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-nor.niedersachsen.de	www.ofd.niedersachsen.de	127
3	2363	Nordenham 	Plaatweg 1	26954	Nordenham	04731/8700	04731/870100		26942	1264	28000000	28001504	BBK OLDENBURG (OLDB)	28050100	63417000	LANDESSPARKASSE OLDENBURG	Mo. - Fr. 8.30 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-nhm.niedersachsen.de	www.ofd.niedersachsen.de	128
3	2364	Oldenburg (Oldenburg) 	91er Straße 4	26121	Oldenburg	0441/2381	(0441) 238-201/2/3		26014	2445	28000000	28001500	BBK OLDENBURG (OLDB)	28050100	423301	LANDESSPARKASSE OLDENBURG	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-ol.niedersachsen.de	www.ofd.niedersachsen.de	129
3	2365	Osnabrück-Land 	Hannoversche Str. 12	49084	Osnabrück	0541/58420	0541/5842450		49002	1280	26500000	26501501	BBK OSNABRUECK	26552286	110007	KREISSPARKASSE MELLE	Mo., Mi., Do. u. Fr. 8.00 - 12.00 Uhr, Di. 12.00 - 17.00 Uhr	Poststelle@fa-os-l.niedersachsen.de	www.ofd.niedersachsen.de	130
3	2366	Osnabrück-Stadt 	Süsterstr. 46/48	49074	Osnabrück	0541/3540	(0541) 354-312		49009	1920	26500000	26501500	BBK OSNABRUECK	26550105	19000	SPARKASSE OSNABRUECK	Mo. - Mi., Fr. 8.00 - 12.00 Uhr, nur Infothek: Do. 12.00 - 17.00 Uhr	Poststelle@fa-os-s.niedersachsen.de	www.ofd.niedersachsen.de	131
3	2367	Quakenbrück 	Lange Straße 37	49610	Quakenbrück	05431/1840	05431/184101		49602	1261	26500000	26501503	BBK OSNABRUECK	26551540	18837179	KR SPK BERSENBRUECK	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-qua.niedersachsen.de	www.ofd.niedersachsen.de	132
3	2368	Vechta 	Rombergstr. 49	49377	Vechta	04441/180	(04441) 18-100	49375			28000000	28001502	BBK OLDENBURG (OLDB)	28050100	70400049	LANDESSPARKASSE OLDENBURG	Mo. - Fr. 8.30 - 12.00 Uhr, Mo. 14.00 - 16.00 Uhr,Mi. 14.00 - 17.00	Poststelle@fa-vec.niedersachsen.de	www.ofd.niedersachsen.de	133
3	2369	Westerstede 	Ammerlandallee 14	26655	Westerstede	04488/5150	04488/515444	26653			28000000	28001503	BBK OLDENBURG (OLDB)	28050100	40465007	LANDESSPARKASSE OLDENBURG	Mo. - Fr. 9.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-wst.niedersachsen.de	www.ofd.niedersachsen.de	134
3	2370	Wilhelmshaven 	Rathausplatz 3	26382	Wilhelmshaven	04421/1830	04421/183111		26354	1462	28000000	28201500	BBK OLDENBURG (OLDB)	28250110	2117000	SPARKASSE WILHELMSHAVEN	Mo. - Fr. 8.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-whv.niedersachsen.de	www.ofd.niedersachsen.de	135
3	2371	Wittmund 	Harpertshausen.Str.2	26409	Wittmund	04462/840	04462/84195		26398	1153	28000000	28201502	BBK OLDENBURG (OLDB)				Mo. - Fr. 8.00 - 12.00 Uhr, Do. 14.00 - 17.00 Uhr	Poststelle@fa-wtm.niedersachsen.de	www.ofd.niedersachsen.de	136
3	2380	Braunschweig für Großbetriebsprüfung	Theodor-Heuss-Str.4a	38122	Braunschweig	0531/80970	(0531) 8097-333		38009	1937							nach Vereinbarung	Poststelle@fa-gbp-bs.niedersachsen.de	www.ofd.niedersachsen.de	137
3	2381	Göttingen für Großbetriebsprüfung	Godehardstr. 6	37073	Göttingen	0551/4070	(0551) 407-448											Poststelle@fa-gbp-goe.niedersachsen.de	www.ofd.niedersachsen.de	138
3	2382	Hannover I für Großbetriebsprüfung	Bischofsholer Damm 15	30173	Hannover	(0511) 8563-01	(0511) 8563-195											Poststelle@fa-gbp-h1.niedersachsen.de	www.ofd.niedersachsen.de	139
3	2383	Hannover II für Großbetriebsprüfung	Bischofsholer Damm 15	30173	Hannover	(0511) 8563-02	(0511) 8563-250		30019	1927								Poststelle@fa-gbp-h2.niedersachsen.de	www.ofd.niedersachsen.de	140
3	2384	Stade für Großbetriebsprüfung	Am Ärztehaus 12	21680	Stade	(04141) 602-0	(04141) 602-60											Poststelle@fa-gbp-std.niedersachsen.de	www.ofd.niedersachsen.de	141
3	2385	Oldenburg für Großbetriebsprüfung	Georgstr. 36	26121	Oldenburg	0441/2381	(0441) 238-522		26014	2445								Poststelle@fa-gbp-ol.niedersachsen.de	www.ofd.niedersachsen.de	142
3	2386	Osnabrück für Großbetriebsprüfung	Johann-Domann-Str. 6	49080	Osnabrück	(0541) 503 800	(0541) 503 888											Poststelle@fa-gbp-os.niedersachsen.de	www.ofd.niedersachsen.de	143
3	2390	Braunschweig für Fahndung und Strafsachen	Rudolf-Steiner-Str. 1	38120	Braunschweig	0531/28510	(0531) 2851-150		38009	1931							nach Vereinbarung	Poststelle@fa-fust-bs.niedersachsen.de	www.ofd.niedersachsen.de	144
3	2391	Hannover für Fahndung und Strafsachen	Göttinger Chaus. 83B	30459	Hannover	(0511) 419-1	(0511) 419-2988		30430	911007								Poststelle@fa-fust-h.niedersachsen.de	www.ofd.niedersachsen.de	145
3	2392	Lüneburg für Fahndung und Strafsachen	Horst-Nickel-Str. 6	21337	Lüneburg	(04131) 8545-600	(04131) 8545-698		21305	1570								Poststelle@fa-fust-lg.niedersachsen.de	www.ofd.niedersachsen.de	146
3	2393	Oldenburg für Fahndung und Strafsachen	Cloppenburger Str. 320	26133	Oldenburg	(0441) 9401-0	(0441) 9401-200		26014	2442								Poststelle@fa-fust-ol.niedersachsen.de	www.ofd.niedersachsen.de	147
4	2457	Bremen-Mitte Bewertung 	Rudolf-Hilferding-Platz 1	28195	Bremen	0421 322-2725	0421 322-2878		28079	10 79 67	29050000	1070110002	BREMER LANDESBANK BREMEN	29050101	109 0901	SPK BREMEN	Zentrale Informations- und Annahmestelle Mo+Do 08:00-18:00,Di+Mi 08:00-16:00,Fr 08:00-15:00	office@FinanzamtMitte.bremen.de		148
4	2471	Bremen-Mitte 	Rudolf-Hilferding-Platz 1	28195	Bremen	0421 322-2725	0421 322-2878	28187	28079	10 79 67	29000000	29001512	BBK BREMEN	29050101	1090646	SPK BREMEN	Zentrale Informations- und Annahmestelle Mo+Do 08:00-18:00,Di+Mi 08:00-16:00,Fr 08:00-15:00	office@FinanzamtMitte.bremen.de		149
4	2472	Bremen-Ost 	Rudolf-Hilferding-Platz 1	28195	Bremen	0421 322-3005	0421 322-3178		28057	10 57 09	29000000	29001513	BBK BREMEN	29050101	1090612	SPK BREMEN	Zentrale Informations- und Annahmestelle Mo+Do 08:00-18:00,Di+Mi 08:00-16:00,Fr 08:00-15:00	office@FinanzamtOst.bremen.de		150
4	2473	Bremen-West 	Rudolf-Hilferding-Platz 1	28195	Bremen	0421 322-3422	0421 322-3478		28057	10 57 29	29000000	29001514	BBK BREMEN	29050101	1090638	SPK BREMEN	Zentrale Informations- und Annahmestelle Mo+Do 08:00-18:00,Di+Mi 08:00-16:00,Fr 08:00-15:00	office@FinanzamtWest.bremen.de		151
4	2474	Bremen-Nord 	Gerhard-Rohlfs-Str. 32	28757	Bremen	0421 6607-1	0421 6607-300		28734	76 04 34	29000000	29001518	BBK BREMEN	29050101	5016001	SPK BREMEN	Zentrale Informations- und Annahmestelle Mo+Do 08:00-18:00,Di+Mi 08:00-16:00,Fr 08:00-14:00	office@FinanzamtNord.bremen.de		152
4	2475	Bremerhaven 	Schifferstr. 2-8	27568	Bremerhaven	0471 486-1	0471 486-370		27516	12 02 42	29200000	29201500	BBK BREMEN EH BREMERHAVEN	29250000	1100068	STE SPK BREMERHAVEN	Zentrale Informations- und Annahmestelle Mo+Do 08:00-18:00,Di+Mi 08:00-16:00,Fr 08:00-15:00	office@FinanzamtBremerhaven.bremen.de		153
4	2476	Bremen-Mitte KraftfahrzeugSt 	Schillerstr. 22	28195	Bremen	0421 322-2725	0421 322-2878		28079	107967	29000000	29001512	BBK BREMEN	29050101	 109 0646	SPK BREMEN	Zentrale Informations- und Annahmestelle Mo+Do 08:00-18:00,Di+Mi 08:00-16:00,Fr 08:00-15:00	office@FinanzamtMitte.bremen.de		154
4	2477	Bremerhaven Bewertung 	Schifferstr. 2 - 8	27568	Bremerhaven	0471 486-1	0471 486-370		27516	12 02 42	29200000	29201500	BBK BREMEN EH BREMERHAVEN	29250000	1100068	STE SPK BREMERHAVEN	Zentrale Informations- und Annahmestelle Mo+Do 08.00-18.00/Di+Mi 08.00-16.00/Fr 08.00-15.00	office@FinanzamtBremerhaven.bremen.de		155
4	2478	Bremen für Großbetriebsprüfung	Schillerstr. 6-7	28195	Bremen	0421 322-4019	0421 322-4078		28057	10 57 69							nach Vereinbarung			156
4	2482	Bremen-Ost Arbeitnehmerbereich 	Rudolf-Hilferding-Platz 1	28195	Bremen	0421 322-3005	0421 322-3178		28057	10 57 09	29000000	29001513	BBK BREMEN	29050101	1090612	SPK BREMEN	Zentrale Informations- und Annahmestelle Mo+Do 08:00-18:00,Di+Mi 08:00-16:00,Fr 08:00-15:00	office@FinanzamtOst.bremen.de		157
4	2484	Bremen-Nord Arbeitnehmerbereic 	Gerhard-Rohlfs-Str. 32	28757	Bremen	0421 6607-1	0421 6607-300		28734	76 04 34	29000000	29001518	BBK BREMEN	29050101	5016001	SPK BREMEN	Zentrale Informations- und Annahmestelle Mo+Do 08:00-18:00,Di+Mi 08:00-16:00,Fr 08:00-14:00	office@FinanzamtNord.bremen.de		158
4	2485	Bremerhaven Arbeitnehmerbereic 	Schifferstr. 2-8	27568	Bremerhaven	0471 486-1	0471 486-370		27516	12 02 42	29200000	29201500	BBK BREMEN EH BREMERHAVEN	29250000	1100068	STE SPK BREMERHAVEN	Zentrale Informations- und Annahmestelle Mo+Do 08:00-18:00,Di+Mi 08:00-16:00,Fr 08:00-15:00	office@FinanzamtBremerhaven.bremen.de		159
6	2601	Alsfeld-Lauterbach Verwaltungsstelle Alsfeld	In der Rambach 11	36304	Alsfeld	06631/790-0	06631/790-555		36292	1263	51300000	51301504	BBK GIESSEN	53051130	1022003	SPARKASSE VOGELSBERGKREIS	Mo u. Mi 8:00-12:00, Do 14:00-18:00 Uhr	poststelle@Finanzamt-Alsfeld-Lauterbach.de	www.Finanzamt-Alsfeld-Lauterbach.de	160
6	2602	Hersfeld-Rotenburg Verwaltungsstelle Bad Hersfeld	Im Stift 7	36251	Bad Hersfeld	06621/933-0	06621/933-333		36224	1451	53200000	53201500	BBK KASSEL EH BAD HERSFELD	53250000	1000016	SPK BAD HERSFELD-ROTENBURG	Mo u. Do 8:00-12:00, Di 14:00-18:00 Uhr	poststelle@Finanzamt-Hersfeld-Rotenburg.de	www.Finanzamt-Hersfeld-Rotenburg.de	161
6	2604	Rheingau-Taunus Verwaltungsst. Bad Schwalbach 	Emser Str.27a	65307	Bad Schwalbach	06124/705-0	06124/705-400		65301	1165	51000000	51001502	BBK WIESBADEN	51050015	393000643	NASS SPK WIESBADEN	Mo-Mi 8:00-15:30, Do 13:30-18:00, Fr 8:00-12:00 Uhr	poststelle@Rheingau-Taunus.de	www.Finanzamt-Rheingau-Taunus.de	163
6	2605	Bensheim 	Berliner Ring 35	64625	Bensheim	06251/15-0	06251/15-267		64603	1351	50800000	50801510	BBK DARMSTADT	50950068	1040005	SPARKASSE BENSHEIM	Mo-Mi 8:00-15:30, Do 13:00-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Bensheim.de	www.Finanzamt-Bensheim.de	164
6	2606	Marburg-Biedenkopf Verwaltungsstelle Biedenkopf	Im Feldchen 2	35216	Biedenkopf	06421/698-0	06421/698-109				51300000	51301514	BBK GIESSEN	53350000	110027303	SPK MARBURG-BIEDENKOPF	Mo u. Fr 8:00-12:00, Mi 14:00-18:00 Uhr Telefon Verwaltungsstelle: 06461/709-0	poststelle@Finanzamt-Marburg-Biedenkopf.de	www.Finanzamt-Marburg-Biedenkopf.de	165
6	2607	Darmstadt 	Soderstraße 30	64283	Darmstadt	06151/102-0	06151/102-1262	64287	64219	110465	50800000	50801500	BBK DARMSTADT	50850049	5093005006	LD BK GZ DARMSTADT	Mo-Mi 8:00-15:30, Do 8:00-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Darmstadt.de	www.Finanzamt-Darmstadt.de	166
6	2608	Dieburg 	Marienstraße 19	64807	Dieburg	06071/2006-0	06071/2006-100		64802	1209	50800000	50801501	BBK DARMSTADT	50852651	33211004	SPARKASSE DIEBURG	Mo-Mi 7:30-15:30, Do 13:30-18:00, Fr 7:30-12:00 Uhr	poststelle@Finanzamt-Dieburg.de	www.Finanzamt-Dieburg.de	167
6	2609	Dillenburg 	Wilhelmstraße 9	35683	Dillenburg	02771/908-0	02771/908-100		35663	1362	51300000	51301509	BBK GIESSEN	51650045	18	BEZ SPK DILLENBURG	Mo-Mi 8:00-15:30, Do 14:00-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Dillenburg.de	www.Finanzamt-Dillenburg.de	168
6	2610	Eschwege-Witzenhausen Verwaltungsstelle Eschwege	Schlesienstraße 2	37269	Eschwege	05651/926-5	05651/926-720	37267	37252	1280	52000000	52001510	BBK KASSEL	52250030	18	SPARKASSE WERRA-MEISSNER	Mo u. Mi 8:00-12:00, Do 14:00-18:00 Uhr	poststelle@Finanzamt-Eschwege-Witzenhausen.de	www.Finanzamt-Eschwege-Witzenhausen.de	169
8	2807	Freiburg-Land 	Stefan-Meier-Str. 133	79104	Freiburg	0761/2040	0761/2043424	79095			68000000	680 015 00	BBK FREIBURG IM BREISGAU	68090000	12222300	VOLKSBANK FREIBURG	ZIA: MO,DI,DO 8-16, MI 8-17:30, FR 8-12 UHR	poststelle@fa-freiburg-land.fv.bwl.de		170
6	2611	Korbach-Frankenberg Verwaltungsstelle Frankenberg 	Geismarer Straße 16	35066	Frankenberg	05631/563-0	05631/563-888				51300000	51301513	BBK GIESSEN	52350005	5001557	SPK WALDECK-FRANKENBERG	Mo, Di u. Do 8:00-15:30, Mi 13:30-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Korbach-Frankenberg.de	www.Finanzamt-Korbach-Frankenberg.de	171
6	2612	Frankfurt am Main II 	Gutleutstraße 122	60327	Frankfurt	069/2545-02	069/2545-2999		60305	110862	50000000	50001504	BBK FILIALE FRANKFURT MAIN	50050000	1600006	LD BK HESS-THUER GZ FFM	Mo-Mi 8:00-15:30, Do 13:30-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Frankfurt-2.de	www.Finanzamt-Frankfurt-am-Main.de	172
6	2613	Frankfurt am Main I 	Gutleutstraße 124	60327	Frankfurt	069/2545-01	069/2545-1999		60305	110861	50000000	50001504	BBK FILIALE FRANKFURT MAIN	50050000	1600006	LD BK HESS-THUER GZ FFM	Mo-Mi 8:00-15:30, Do 13:30-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Frankfurt-1.de	www.Finanzamt-Frankfurt-am-Main.de	173
6	2614	Frankfurt am Main IV 	Gutleutstraße 118	60327	Frankfurt	069/2545-04	069/2545-4999		60305	110864	50000000	50001504	BBK FILIALE FRANKFURT MAIN	50050000	1600006	LD BK HESS-THUER GZ FFM	Mo u. Mi 8:00-12:00, Do 14:00-18:00 Uhr	poststelle@Finanzamt-Frankfurt-4.de	www.Finanzamt-Frankfurt-am-Main.de	174
6	2615	Frankfurt/M. V-Höchst Verwaltungsstelle Höchst	Hospitalstraße 16 a	65929	Frankfurt	069/2545-05	069/2545-5999				50000000	50001502	BBK FILIALE FRANKFURT MAIN	50050201	608604	FRANKFURTER SPK FRANKFURT	Mo u. Mi 8:00-12:00, Do 14:00-18:00 Uhr Telefon Verwaltungsstelle: 069/30830-0	poststelle@Finanzamt-Frankfurt-5-Hoechst.de	www.Finanzamt-Frankfurt-am-Main.de	175
6	2616	Friedberg (Hessen) 	Leonhardstraße 10 - 12	61169	Friedberg	06031/49-1	06031/49-333		61143	100362	51300000	51301506	BBK GIESSEN	51850079	50000400	SPARKASSE WETTERAU	Di 8:00-12:00, Do 14:00-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Friedberg.de	www.Finanzamt-Friedberg.de	176
6	2617	Bensheim Außenstelle Fürth	Erbacher Straße 23	64658	Fürth	06253/206-0	06253/206-10		64654	1154	50800000	50801510	BBK DARMSTADT	50950068	1040005	SPARKASSE BENSHEIM		poststelle@Finanzamt-Bensheim.de	www.Finanzamt-Bensheim.de	177
6	2618	Fulda 	Königstraße 2	36037	Fulda	0661/924-01	0661/924-1606		36003	1346	53000000	53001500	BBK KASSEL EH FULDA	53050180	49009	SPARKASSE FULDA	Mo-Mi 8:00-15:30, Do 14:00-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Fulda.de	www.Finanzamt-Fulda.de	178
6	2619	Gelnhausen 	Frankfurter Straße 14	63571	Gelnhausen	06051/86-0	06051/86-299	63569	63552	1262	50600000	50601502	BBK FRANKFURT EH HANAU	50750094	2008	KREISSPARKASSE GELNHAUSEN	Mo u. Mi 8:00-12:00, Do 14:30-18:00 Uhr	poststelle@Finanzamt-Gelnhausen.de	www.Finanzamt-Gelnhausen.de	179
6	2620	Gießen 	Schubertstraße 60	35392	Gießen	0641/4800-100	0641/4800-1590	35387	35349	110440				51300000	51301500	BBK GIESSEN	Mo-Mi 8:00-15:30,Do 14:00-18:00, Fr 8:00-12:00 Uhr	info@Finanzamt-Giessen.de	www.Finanzamt-Giessen.de	180
6	2621	Groß-Gerau 	Europaring 11-13	64521	Groß-Gerau	06152/170-01	06152/170-601	64518	64502	1262	50800000	50801502	BBK DARMSTADT	50852553	1685	KR SPK GROSS-GERAU	Mo-Mi 8:00-15.30, Do 14:00-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Gross-Gerau.de	www.Finanzamt-Gross-Gerau.de	181
6	2622	Hanau 	Am Freiheitsplatz 2	63450	Hanau	06181/101-1	06181/101-501	63446	63404	1452	50600000	50601500	BBK FRANKFURT EH HANAU	50650023	50104	SPARKASSE HANAU	Mo u. Mi 7:30-12:00, Do 14:30-18:00 Uhr	poststelle@Finanzamt-Hanau.de	www.Finanzamt-Hanau.de	182
6	2623	Kassel-Hofgeismar Verwaltungsstelle Hofgeismar	Altstädter Kirchplatz 10	34369	Hofgeismar	0561/7207-0	0561/7207-2500				52000000	52001501	BBK KASSEL	52050353	100009202	KASSELER SPARKASSE	Di, Mi u. Fr 8:00-12:00, Do 15:00-18:00 Uhr Telefon Verwaltungsstelle: 05671/8004-0	poststelle@Finanzamt-Kassel-Hofgeismar.de	www.Finanzamt-Kassel.de	183
6	2624	Schwalm-Eder Verwaltungsstelle Fritzlar	Georgengasse 5	34560	Fritzlar	05622/805-0	05622/805-111		34551	1161	52000000	52001502	BBK KASSEL	52052154	110007507	KREISSPARKASSE SCHWALM-EDER	Mo u. Fr 8:00-12:00, Mi 14:00-18:00 Uhr	poststelle@Finanzamt-Schwalm-Eder.de	www.Finanzamt-Schwalm-Eder.de	184
6	2625	Kassel-Spohrstraße 	Spohrstraße 7	34117	Kassel	0561/7208-0	0561/7208-408	34111	34012	101249	52000000	52001500	BBK KASSEL	52050000	4091300006	LANDESKREDITKASSE KASSEL	Mo u. Fr 7:30-12:00, Mi 14:00-18:00 Uhr	poststelle@Finanzamt-Kassel-Spohrstrasse.de	www.Finanzamt-Kassel.de	185
6	2626	Kassel-Hofgeismar Verwaltungsstelle Kassel	Goethestraße 43	34119	Kassel	0561/7207-0	0561/7207-2500	34111	34012	101229	52000000	52001500	BBK KASSEL	52050000	4091300006	LANDESKREDITKASSE KASSEL	Mo, Mi u. Fr 8:00-12:00, Mi 14:00-18:00 Uhr	poststelle@Finanzamt-Kassel-Hofgeismar.de	www.Finanzamt-Kassel.de	186
6	2627	Korbach-Frankenberg Verwaltungsstelle Korbach	Medebacher Landstraße 29	34497	Korbach	05631/563-0	05631/563-888	34495	34482	1240	52000000	52001509	BBK KASSEL	52350005	19588	SPK WALDECK-FRANKENBERG	Mo, Mi u. Fr 8:00-12:00, Do 15:30-18:00 Uhr	poststelle@Finanzamt-Korbach-Frankenberg.de	www.Finanzamt-Korbach-Frankenberg.de	187
6	2628	Langen 	Zimmerstraße 27	63225	Langen	06103/591-01	06103/591-285	63222	63202	1280	50000000	50001511	BBK FILIALE FRANKFURT MAIN	50592200	31500	VB DREIEICH	Mo, Mi u. Do 8:00-15:30, Di 13:30-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Langen.de	www.Finanzamt-Langen.de	188
6	2629	Alsfeld-Lauterbach Verwaltungsstelle Lauterbach	Bahnhofstraße 69	36341	Lauterbach	06631/790-0	06631/790-555	36339			53000000	53001501	BBK KASSEL EH FULDA	53051130	60100509	SPARKASSE VOGELSBERGKREIS	Mo u. Mi 8:00-12:00, Do 14:00-18:00 Uhr Telefon Verwaltungsstelle: 06641/188-0	poststelle@Finanzamt-Alsfeld-Lauterbach.de	www.Finanzamt-Alsfeld-Lauterbach.de	189
6	2630	Limburg-Weilburg Verwaltungsstelle Limburg	Walderdorffstraße 11	65549	Limburg	06431/208-1	06431/208-294	65547	65534	1465	51000000	51001507	BBK WIESBADEN	51050015	535054800	NASS SPK WIESBADEN	Mo-Mi 8:00-15:30, Do 8:00-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Limburg-Weilburg.de	www.Finanzamt-Limburg-Weilburg.de	190
6	2631	Marburg-Biedenkopf Verwaltungsstelle Marburg	Robert-Koch-Straße 7	35037	Marburg	06421/698-0	06421/698-109	35034	35004	1469	51300000	51301512	BBK GIESSEN	53350000	11517	SPK MARBURG-BIEDENKOPF	Mo-Mi 8:00-15:30, Do 14:00-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Marburg-Biedenkopf.de	www.Finanzamt-Marburg-Biedenkopf.de	191
6	2632	Schwalm-Eder Verwaltungsstelle Melsungen	Kasseler Straße 31 (Schloß)	34212	Melsungen	05622/805-0	05622/805-111				52000000	52001503	BBK KASSEL	52052154	10060002	KREISSPARKASSE SCHWALM-EDER	Mo u. Fr 8:00-12:00, Mi 14:00-18:00 Uhr Telefon Verwaltungsstelle: 05661/706-0	poststelle@Finanzamt-Schwalm-Eder.de	www.Finanzamt-Schwalm-Eder.de	192
6	2633	Michelstadt 	Erbacher Straße 48	64720	Michelstadt	06061/78-0	06061/78-100		64712	3180	50800000	50801503	BBK DARMSTADT	50851952	40041451	SPK ODENWALDKREIS ERBACH	Mo, Di u. Do 8:00-15:30, Mi 13:30-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Michelstadt.de	www.Finanzamt-Michelstadt.de	193
6	2634	Nidda 	Schillerstraße 38	63667	Nidda	06043/805-0	06043/805-159		63658	1180	50600000	50601501	BBK FRANKFURT EH HANAU	51850079	150003652	SPARKASSE WETTERAU	Mo, Di u. Do 7:30-16:00, Mi 13:30-18:00, Fr 7:00-12:00 Uhr	poststelle@Finanzamt-Nidda.de	www.Finanzamt-Nidda.de	194
6	2635	Offenbach am Main-Stadt 	Bieberer Straße 59	63065	Offenbach	069/8091-1	069/8091-2400	63063	63005	100563	50000000	50001500	BBK FILIALE FRANKFURT MAIN	50550020	493	STE SPK OFFENBACH	Mo, Di u. Do 7:30-15:30, Mi 13:00-18:00, Fr 7:30-12:00 Uhr	poststelle@Finanzamt-Offenbach-Stadt.de	www.Finanzamt-Offenbach.de	195
6	2636	Hersfeld-Rotenburg Verwaltungsstelle Rotenburg	Dickenrücker Straße 12	36199	Rotenburg	06621/933-0	06621/933-333				52000000	52001504	BBK KASSEL	53250000	50000012	SPK BAD HERSFELD-ROTENBURG	Mo u. Fr 8:00-12:00, Mi 14:00-18:00 Uhr Telefon Verwaltungsstelle: 06623/816-0	poststelle@Finanzamt-Hersfeld-Rotenburg.de	www.Finanzamt-Hersfeld-Rotenburg.de	196
6	2637	Rheingau-Taunus Verwaltungsstelle Rüdesheim	Hugo-Asbach-Straße 3 - 7	65385	Rüdesheim	06124/705-0	06124/705-400				51000000	51001501	BBK WIESBADEN	51050015	455022800	NASS SPK WIESBADEN	Mo u. Fr 8:00-12:00, Mi 14:00-18:00 Uhr Telefon Verwaltungsstelle: 06722/405-0	poststelle@Finanzamt-Rheingau-Taunus.de	www.Finanzamt-Rheingau-Taunus.de	197
6	2638	Limburg-Weilburg Verwaltungsstelle Weilburg	Kruppstraße 1	35781	Weilburg	06431/208-1	06431/208-294	35779			51000000	51001511	BBK WIESBADEN	51151919	100000843	KR SPK WEILBURG	Mo-Mi 8:00-16:00, Do 14:00-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Limburg-Weilburg.de	www.Finanzamt-Limburg-Weilburg.de	198
6	2639	Wetzlar 	Frankfurter Straße 59	35578	Wetzlar	06441/202-0	06441/202-6810	35573	35525	1520	51300000	51301508	BBK GIESSEN	51550035	46003	SPARKASSE WETZLAR	Mo u. Mi 8:00-12:00, Do 14:00-18:00 Uhr	poststelle@Finanzamt-Wetzlar.de	www.Finanzamt-Wetzlar.de	199
6	2640	Wiesbaden I 	Dostojewskistraße 8	65187	Wiesbaden	0611/813-0	0611/813-1000	65173	65014	2469	51000000	51001500	BBK WIESBADEN	51050015	100061600	NASS SPK WIESBADEN	Mo, Di u. Do 8:00-15:30, Mi 13:30-18:00, Fr 7:00-12:00 Uhr	poststelle@Finanzamt-Wiesbaden-1.de	www.Finanzamt-Wiesbaden.de	200
6	2641	Eschwege-Witzenhausen Verwaltungsstelle Witzenhausen	Südbahnhofstraße 37	37213	Witzenhausen	05651/926-5	05651/926-720				52000000	52001505	BBK KASSEL	52250030	50000991	SPARKASSE WERRA-MEISSNER	Mo u. Fr 8:00-12:00, Mi 14:00-18:00 Uhr Telefon Verwaltungsstelle: 05542/602-0	poststelle@Finanzamt-Eschwege-Witzenhausen.de	www.Finanzamt-Eschwege-Witzenhausen.de	201
6	2642	Schwalm-Eder Verwaltungsstelle Schwalmstadt	Landgraf-Philipp-Straße 15	34613	Schwalmstadt	05622/805-0	05622/805-111				52000000	52001506	BBK KASSEL	52052154	200006641	KREISSPARKASSE SCHWALM-EDER	Mo u. Mi 8:00-12:00, Do 14:00-18:00 Uhr Telefon Verwaltungsstelle: 06691/738-0	poststelle@Finanzamt-Schwalm-Eder.de	www.Finanzamt-Schwalm-Eder.de	202
6	2643	Wiesbaden II 	Dostojewskistraße 8	65187	Wiesbaden	0611/813-0	0611/813-2000	65173	65014	2469	51000000	51001500	BBK WIESBADEN	51050015	100061600	NASS SPK WIESBADEN	Mo, Di u. Do 8:00-15:30, Mi 13:30-18:00, Fr 7:00-12:00 Uhr	poststelle@Finanzamt-Wiesbaden-2.de	www.Finanzamt-Wiesbaden.de	203
6	2644	Offenbach am Main-Land 	Bieberer Straße 59	63065	Offenbach	069/8091-1	069/8091-3400	63063	63005	100552	50000000	50001500	BBK FILIALE FRANKFURT MAIN	50550020	493	STE SPK OFFENBACH	Mo, Di u. Do 7:30-15:30, Mi 13:00-18:00, Fr 7:30-12:00 Uhr	poststelle@Finanzamt-Offenbach-Land.de	www.Finanzamt-Offenbach.de	204
6	2645	Frankfurt am Main III 	Gutleutstraße 120	60327	Frankfurt	069/2545-03	069/2545-3999		60305	110863	50000000	50001504	BBK FILIALE FRANKFURT MAIN	50050000	1600006	LD BK HESS-THUER GZ FFM	Mo u. Mi 8:00-12:00, Do 14:00-18:00 Uhr	poststelle@Finanzamt-Frankfurt-3.de	wwww.Finanzamt-Frankfurt-am-Main.de	205
6	2646	Hofheim am Taunus 	Nordring 4 - 10	65719	Hofheim	06192/960-0	06192/960-412	65717	65703	1380	50000000	50001503	BBK FILIALE FRANKFURT MAIN	51250000	2000008	TAUNUS-SPARKASSE BAD HOMBG	Mo-Mi 8:00-15:30, Do 13:30-18:00, Fr 8:00-12:00 Uhr	poststelle@Finanzamt-Hofheim-am-Taunus.de	www.Finanzamt-Hofheim-am-Taunus.de	206
6	2647	Frankfurt/M. V-Höchst Verwaltungsstelle Frankfurt	Gutleutstraße 116	60327	Frankfurt	069/2545-05	069/2545-5999		60305	110865	50000000	50001504	BBK FILIALE FRANKFURT MAIN	50050000	1600006	LD BK HESS-THUER GZ FFM	Mo u. Mi 8:00-12:00, Do 14:00-18:00 Uhr	poststelle@Finanzamt-Frankfurt-5-Hoechst.de	www.Finanzamt-Frankfurt-am-Main.de	207
7	2701	Bad Neuenahr-Ahrweiler 	Römerstr. 5	53474	Bad Neuenahr-Ahrweiler	02641/3820	02641/38212000		53457	1209	55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-aw.fin-rlp.de		208
7	2702	Altenkirchen-Hachenburg 	Frankfurter Str. 21	57610	Altenkirchen	02681/860	02681/8610090	57609	57602	1260	55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-ak.fin-rlp.de	www.finanzamt-altenkirchen-hachenburg.de	209
7	2703	Bingen-Alzey Aussenstelle Alzey	Rochusallee 10	55411	Bingen	06721/7060	06721/70614080	55409	55382		55050000	901	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR Telefon-Nr. Aussenstelle: 06731/4000	Poststelle@fa-bi.fin-rlp.de	www.finanzamt-bingen-alzey.de	210
7	2706	Bad Kreuznach 	Ringstr. 10	55543	Bad Kreuznach	0671/7000	0671/70011702	55541	55505	1552	55050000	901	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-kh.fin-rlp.de	www.finanzamt-bad-kreuznach.de	211
7	2707	Bernkastel-Wittlich Aussenstelle Bernkastel-Kues	Unterer Sehlemet 15	54516	Wittlich	06571/95360	06571/953613400		54502	1240	55050000	902	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR Telefon-Nr. Aussenstelle: 06531/5060	Poststelle@fa-wi.fin-rlp.de	www.finanzamt-bernkastel-wittlich.de	212
7	2708	Bingen-Alzey 	Rochusallee 10	55411	Bingen	06721/7060	06721/70614080	55409	55382		55050000	901	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-bi.fin-rlp.de	www.finanzamt-bingen-alzey.de	213
7	2709	Idar-Oberstein 	Hauptstraße 199	55743	Idar-Oberstein	06781/680	06781/6818333		55708	11820	55050000	901	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-io.fin-rlp.de	www.finanzamt-idar-oberstein.de	214
7	2710	Bitburg-Prüm 	Kölner Straße 20	54634	Bitburg	06561/6030	06561/60315090		54622	1252	55050000	902	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-bt.fin-rlp.de	www.finanzamt-bitburg-pruem.de	215
7	2713	Daun 	Berliner Straße 1	54550	Daun	06592/95790	06592/957916175		54542	1160	55050000	902	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-da.fin-rlp.de	www.finanzamt-daun.de	216
7	2714	Montabaur-Diez Aussenstelle Diez	Koblenzer Str. 15	56410	Montabaur	02602/1210	02602/12127099	56409	56404	1461	55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR Telefon-Nr. Aussenstelle: 06432/5040	Poststelle@fa-mt.fin-rlp.de	www.finanzamt-montabaur-diez.de	217
7	2715	Frankenthal 	Friedrich-Ebert-Straße 6	67227	Frankenthal	06233/49030	06233/490317082	67225	67203	1324	55050000	910	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-ft.fin-rlp.de	www.finanzamt-frankenthal.de	218
7	2716	Speyer-Germersheim Aussenstelle Germersheim	Johannesstr. 9-12	67346	Speyer	06232/60170	06232/601733431	67343	67323	1309	55050000	910	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR Telefon-Nr. Aussenstelle: 07274/9500	Poststelle@fa-sp.fin-rlp.de	www.finanzamt-speyer-germersheim.de	219
7	2718	Altenkirchen-Hachenburg Aussenstelle Hachenburg	Frankfurter Str. 21	57610	Altenkirchen	02681/860	02681/8610090	57609	57602	1260	55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR Telefon-Nr. Aussenstelle: 02662/94520	Poststelle@fa-ak.fin-rlp.de	www.finanzamt-altenkirchen-hachenburg.de	220
7	2719	Kaiserslautern 	Eisenbahnstr. 56	67655	Kaiserslautern	0631/36760	0631/367619500	67653	67621	3360	55050000	901	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-kl.fin-rlp.de	www.finanzamt-kaiserslautern.de	221
7	2721	Worms-Kirchheimbolanden Aussenstelle Kirchheimbolanden	Karlsplatz 6	67549	Worms	06241/30460	06241/304635060	67545			55050000	901	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR Telefon-Nr. Aussenstelle: 06352/4070	Poststelle@fa-wo.fin-rlp.de	www.finanzamt-worms-kirchheimbolanden.de	222
7	2722	Koblenz 	Ferdinand-Sauerbruch-Str. 19	56073	Koblenz	0261/49310	0261/493120090	56060	56007	709	55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-ko.fin-rlp.de	www.finanzamt-koblenz.de	223
7	2723	Kusel-Landstuhl 	Trierer Str. 46	66869	Kusel	06381/99670	06381/996721060		66864	1251	55050000	901	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-ku.fin-rlp.de	www.finanzamt-kusel-landstuhl.de	224
7	2724	Landau 	Weißquartierstr. 13	76829	Landau	06341/9130	06341/91322100	76825	76807	1760u.1780	55050000	910	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-ld.fin-rlp.de		225
7	2725	Kusel-Landstuhl Aussenstelle Landstuhl	Trierer Str. 46	66869	Kusel	06381/99670	06381/996721060		66864	1251	55050000	901	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR Telefon-Nr. Aussenstelle: 06371/61730	Poststelle@fa-ku.fin-rlp.de	www.finanzamt-kusel-landstuhl.de	226
7	2726	Mainz-Mitte 	Schillerstr. 13	55116	Mainz	06131/2510	06131/25124090		55009	1980	55050000	901	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-mz.fin-rlp.de	www.finanzamt-mainz-mitte.de	227
7	2727	Ludwigshafen 	Bayernstr. 39	67061	Ludwigshafen	0621/56140	0621/561423051		67005	210507	55050000	910	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-lu.fin-rlp.de	www.finanzamt-ludwigshafen.de	228
7	2728	Mainz-Süd 	Emy-Roeder-Str. 3	55129	Mainz	06131/5520	06131/55225272		55071	421365	55050000	901	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-ms.fin-rlp.de	www.finanzamt-mainz-sued.de	229
7	2729	Mayen 	Westbahnhofstr. 11	56727	Mayen	02651/70260	02651/702626090		56703	1363	55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-my.fin-rlp.de	www.finanzamt-mayen.de	230
7	2730	Montabaur-Diez 	Koblenzer Str. 15	56410	Montabaur	02602/1210	02602/12127099	56409	56404	1461	55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-mt.fin-rlp.de	www.finanzamt-montabaur-diez.de	231
7	2731	Neustadt 	Konrad-Adenauer-Str. 26	67433	Neustadt	06321/9300	06321/93028600	67429	67404	100 465	55050000	910	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-nw.fin-rlp.de		232
7	2732	Neuwied 	Augustastr. 54	56564	Neuwied	02631/9100	02631/91029906	56562	56505	1561	55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-nr.fin-rlp.de		233
7	2735	Pirmasens-Zweibrücken 	Kaiserstr. 2	66955	Pirmasens	06331/7110	06331/71130950	66950	66925	1662	55050000	910	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-ps.fin-rlp.de	www.finanzamt-pirmasens-zweibruecken.de	234
7	2736	Bitburg-Prüm Aussenstelle Prüm	Kölner Str. 20	54634	Bitburg	06561/6030	06561/60315093		54622	1252	55050000	902	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR Telefon-Nr. Aussenstelle: 06551/9400	Poststelle@fa-bt.fin-rlp.de	www.finanzamt-bitburg-pruem.de	235
7	2738	Sankt Goarshausen-Sankt Goar Aussenstelle Sankt Goar	Wellmicher Str. 79	56346	St. Goarshausen	06771/95900	06771/959031090		56342		55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR Telefon-Nr. Aussenstelle: 06741/98100	Poststelle@fa-gh.fin-rlp.de	www.finanzamt-sankt-goarshausen-sankt-goar.de	236
7	2739	Sankt Goarshausen-Sankt Goar 	Wellmicher Str. 79	56346	St. Goarshausen	06771/95900	06771/959031090		56342		55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-gh.fin-rlp.de	www.finanzamt-sankt-goarshausen-sankt-goar.de	237
7	2740	Simmern-Zell 	Brühlstraße 3	55469	Simmern	06761/8550	06761/85532053		55464	440	55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-si.fin-rlp.de		238
7	2741	Speyer-Germersheim 	Johannesstr. 9-12	67346	Speyer	06232/60170	06232/601733431	67343	67323	1309	55050000	910	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-sp.fin-rlp.de	www.finanzamt-speyer-germersheim.de	239
7	2742	Trier 	Hubert-Neuerburg-Str. 1	54290	Trier	0651/93600	0651/936034900		54207	1750u.1760	55050000	902	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-tr.fin-rlp.de	www.finanzamt-trier.de	240
7	2743	Bernkastel-Wittlich 	Unterer Sehlemet 15	54516	Wittlich	06571/95360	06571/953613400		54502	1240	55050000	902	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-wi.fin-rlp.de	www.finanzamt-bernkastel-wittlich.de	241
7	2744	Worms-Kirchheimbolanden 	Karlsplatz 6	67549	Worms	06241/30460	06241/304635060	67545			55050000	901	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR	Poststelle@fa-wo.fin-rlp.de	www.finanzamt-worms-kirchheimbolanden.de	242
7	2745	Simmern-Zell Aussenstelle Zell	Brühlstr. 3	55469	Simmern	06761/8550	06761/85532053		55464	440	55050000	908	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR Telefon-Nr. Aussenstelle: 06542/7090	Poststelle@fa-si.fin-rlp.de		243
7	2746	Pirmasens-Zweibrücken Aussenstelle Zweibrücken	Kaiserstr. 2	66955	Pirmasens	06331/7110	06331/71130950	66950	66925	1662	55050000	910	LRP GZ MAINZ				8.00-17.00 MO-MI 8.00-18.00 DO 8.00-13.00 FR Telefon-Nr. Aussenstelle: 06332/80680	Poststelle@fa-ps.fin-rlp.de	www.finanzamt-pirmasens-zweibruecken.de	244
8	2801	Achern 	Allerheiligenstr. 10	77855	Achern	07841/6940	07841/694136	77843	77843	1260	66000000	66001518	BBK KARLSRUHE	66450050	88013009	SPARKASSE OFFENBURG-ORTENAU	MO-DO 8-12.30+13.30-15.30,DO-17.30,FR 8-12 H	poststelle@fa-achern.fv.bwl.de		245
8	2804	Donaueschingen 	Käferstr. 25	78166	Donaueschingen	0771/8080	0771/808359	78153	78153	1269	69400000	694 01501	BBK VILLINGEN-SCHWENNINGEN	69421020	6204700600	BW BANK DONAUESCHINGEN	MO-MI 8-16 UHR, DO 8-17.30 UHR, FR 8-12 UHR	poststelle@fa-donaueschingen.fv.bwl.de		246
8	2805	Emmendingen 	Bahnhofstr. 3	79312	Emmendingen	07641/4500	07641/450350	79305	79305	1520	68000000	680 01507	BBK FREIBURG IM BREISGAU	68050101	20066684	SPK FREIBURG-NOERDL BREISGA	MO-MI 7:30-15:30,DO 7:30-17:00,FR 7:30-12:00	poststelle@fa-emmendingen.fv.bwl.de		247
8	2806	Freiburg-Stadt 	Sautierstr. 24	79104	Freiburg	0761/2040	0761/2043295	79079			68000000	680 01501	BBK FREIBURG IM BREISGAU	68020020	4402818100	BW BANK FREIBURG BREISGAU	MO, DI, DO 7.30-16,MI 7.30-17.30, FR 7.30-12	poststelle@fa-freiburg-stadt.fv.bwl.de		248
8	2808	Kehl 	Ludwig-Trick-Str. 1	77694	Kehl	07851/8640	07851/864108	77676	77676	1640	66400000	664 01501	BBK FREIBURG EH OFFENBURG	66451862	-6008	SPK HANAUERLAND KEHL	MO,DI,MI 7.45-15.30, DO -17.30, FR -12.00UHR	poststelle@fa-kehl.fv.bwl.de		249
8	2809	Konstanz 	Bahnhofplatz 12	78462	Konstanz	07531/2890	07531/289312	78459			69400000	69001500	BBK VILLINGEN-SCHWENNINGEN	69020020	6604947900	BW BANK KONSTANZ	MO,DI,DO 7.30-15.30,MI 7.30-17.00,FR 7.30-12	poststelle@fa-konstanz.fv.bwl.de		250
8	2810	Lahr 	Gerichtstr. 5	77933	Lahr	07821/2830	07821/283100		77904	1466	66000000	66001527	BBK KARLSRUHE	66450050	76103333	SPARKASSE OFFENBURG-ORTENAU	MO,DI,DO 7:30-16:00, MI 7:30-17:30, FR 7:30-12:00	poststelle@fa-lahr.fv.bwl.de		251
8	2811	Lörrach 	Luisenstr. 10 a	79539	Lörrach	07621/1730	07621/173245	79537			68000000	68301500	BBK FREIBURG IM BREISGAU	68320020	4602600100	BW BANK LOERRACH	MO-MI 7.00-15.30/DO 7.00-17.30/FR 7.00-12.00	poststelle@fa-loerrach.fv.bwl.de		252
8	2812	Müllheim 	Goethestr. 11	79379	Müllheim	07631/18900	(07631)189-190	79374	79374	1461	68000000	680 01511	BBK FREIBURG IM BREISGAU	68351865	802 888 8	SPARKASSE MARKGRAEFLERLAND	MO-MI 7,30-15,30 DO 7,30-17,30 FR 7,30-12,00	poststelle@fa-muellheim.fv.bwl.de		253
8	2813	Titisee-Neustadt 	Goethestr. 5	79812	Titisee-Neustadt	07651/2030	07651/203110		79812	12 69	68000000	680 015 10	BBK FREIBURG IM BREISGAU	68051004	4040408	SPK HOCHSCHWARZWALD T-NEUST	MO-MI 7.30-15.30,DO 7.30-17.30,FR 7.30-12.30	poststelle@fa-titisee-neustadt.fv.bwl.de		254
8	2814	Offenburg 	Zeller Str. 1- 3	77654	Offenburg	0781/9330	0781/9332444	77604	77604	1440	68000000	664 01500	BBK FREIBURG IM BREISGAU	66420020	4500000700	BW BANK OFFENBURG	MO-DO 7.30-15.30 DURCHGEHEND,MI -18.00,FR-12	poststelle@fa-offenburg.fv.bwl.de		255
8	2815	Oberndorf 	Brandeckerstr. 4	78727	Oberndorf	07423/8150	07423/815107	78721	78721	1240	69400000	694 01506	BBK VILLINGEN-SCHWENNINGEN	64250040	813 015	KR SPK ROTTWEIL	ZIA:MO,DI,DO 8-16,MI 8-17:30,FR 8-12 UHR	poststelle@fa-oberndorf.fv.bwl.de		256
8	2816	Bad Säckingen 	Werderstr. 5	79713	Bad Säckingen	07761/5660	07761/566126	79702	79702	1148	68000000	683 015 02	BBK FREIBURG IM BREISGAU				MO,DI,DO 8-15.30, MI 8-17.30, FR 8-12 UHR	poststelle@fa-badsaeckingen.fv.bwl.de		257
8	2818	Singen 	Alpenstr. 9	78224	Singen	07731/8230	07731/823650		78221	380	69000000	69001507	BBK VILL-SCHWEN EH KONSTANZ	69220020	6402000100	BW BANK SINGEN	MO-DO 7:30-15:30, MI bis 17:30, FR 7:30-12:00	poststelle@fa-singen.fv.bwl.de		258
8	2819	Rottweil 	Körnerstr. 28	78628	Rottweil	0741/2430	0741/2432194	78612	78612	1252	69400000	69401505	BBK VILLINGEN-SCHWENNINGEN	64250040	136503	KR SPK ROTTWEIL	MO-MI 8-16, DO 8-18, FR 8-12 UHR	poststelle@fa-rottweil.fv.bwl.de		259
8	2820	Waldshut-Tiengen 	Bahnhofstr. 11	79761	Waldshut-Tiengen	07741/6030	07741/603213	79753	79753	201360	68000000	68301501	BBK FREIBURG IM BREISGAU	68452290	14449	SPARKASSE HOCHRHEIN	MO-MI 8-15.30,DO 8-17.30,FR 8-12 UHR	poststelle@fa-waldshut-tiengen.fv.bwl.de		260
8	2821	Tuttlingen 	Zeughausstr. 91	78532	Tuttlingen	07461/980	07461/98303		78502	180	69400000	69401502	BBK VILLINGEN-SCHWENNINGEN	64350070	251	KR SPK TUTTLINGEN	MO-MI8-15.30,DO8-17.30,FR8-12.00UHR	poststelle@fa-tuttlingen.fv.bwl.de		261
8	2822	Villingen-Schwenningen 	Weiherstr. 7	78050	Villingen-Schwenningen	07721/923-0	07721/923-100	78045			69400000	69401500	BBK VILLINGEN-SCHWENNINGEN				MO-MI 8-16UHR,DO 8-17.30UHR,FR 8-12UHR	poststelle@fa-villingen-schwenningen.fv.bwl.de		262
8	2823	Wolfach 	Hauptstr. 55	77709	Wolfach	07834/9770	07834/977-169	77705	77705	1160	66400000	664 01502	BBK FREIBURG EH OFFENBURG	66452776	-31956	SPK WOLFACH	MO-MI 7.30-15.30,DO 7.30-17.30,FR 7.30-12.00	poststelle@fa-wolfach.fv.bwl.de		263
8	2830	Bruchsal 	Schönbornstr. 1-5	76646	Bruchsal	07251/740	07251/742111	76643	76643	3021	66000000	66001512	BBK KARLSRUHE	66350036	50	SPK KRAICHGAU	SERVICEZENTRUM:MO-MI8-15:30DO8-17:30FR8-1200	poststelle@fa-bruchsal.fv.bwl.de		264
8	2831	Ettlingen 	Pforzheimer Str. 16	76275	Ettlingen	07243/5080	07243/508295	76257	76257	363	66000000	66001502	BBK KARLSRUHE	66051220	1043009	SPARKASSE ETTLINGEN	MO+DI 8-15.30,MI 7-15.30,DO 8-17.30,FR 8-12	poststelle@fa-ettlingen.fv.bwl.de		265
8	2832	Heidelberg 	Kurfürsten-Anlage 15-17	69115	Heidelberg	06221/590	06221/592355	69111			67000000	67001510	BBK MANNHEIM	67220020	5302059000	BW BANK HEIDELBERG	ZIA:MO-DO 7.30-15.30, MI - 17.30, FR - 12.00	poststelle@fa-heidelberg.fv.bwl.de		266
8	2833	Baden-Baden 	Stephanienstr. 13 + 15	76530	Baden-Baden	07221/3590	07221/359320	76520			66000000	66001516	BBK KARLSRUHE	66220020	4301111300	BW BANK BADEN-BADEN	MO,DI,DO 8-16 UHR,MI 8-17.30 UHR,FR 8-12 UHR	poststelle@fa-baden-baden.fv.bwl.de		267
8	2834	Karlsruhe-Durlach 	Prinzessenstr. 2	76227	Karlsruhe	0721/9940	0721/9941235	76225	76203	410326	66000000	66001503	BBK KARLSRUHE				MO-DO 8-15.30,MI 8-17.30,FR 8-12	poststelle@fa-karlsruhe-durlach.fv.bwl.de		268
8	2835	Karlsruhe-Stadt 	Schlossplatz 14	76131	Karlsruhe	0721/1560	(0721) 156-1000				66000000	66001501	BBK KARLSRUHE	66020020	4002020800	BW BANK KARLSRUHE	MO-DO 7.30-15.30 MI -17.30 FR 7.30-12.00	poststelle@fa-karlsruhe-stadt.fv.bwl.de		269
8	2836	Bühl 	Alban-Stolz-Str. 8	77815	Bühl	07223/8030	07223/3625	77815			66000000	66001525	BBK KARLSRUHE	66220020	4301111300	BW BANK BADEN-BADEN	MO,DI,DO=8-16UHR, MI=8-17.30UHR,FR=8-12UHR	poststelle@fa-buehl.fv.bwl.de		270
8	2837	Mannheim-Neckarstadt 	L3, 10	68161	Mannheim	0621/2920	0621/292-1010	68150			67000000	67001500	BBK MANNHEIM	67020020	5104719900	BW BANK MANNHEIM	MO,DI,DO7.30-15.30,MI7.30-17.30,FR7.30-12.00	poststelle@fa-mannheim-neckarstadt.fv.bwl.de		271
8	2838	Mannheim-Stadt 	L3, 10	68161	Mannheim	0621/2920	2923640	68150			67000000	670 01500	BBK MANNHEIM	67020020	5104719900	BW BANK MANNHEIM	MO,DI,DO7.30-15.30,MI7.30.17.30,FR7.30-12.00	poststelle@fa-mannheim-stadt.fv.bwl.de		272
8	2839	Rastatt 	An der Ludwigsfeste 3	76437	Rastatt	07222/9780	07222/978330	76404	76404	1465	66000000	66001519	BBK KARLSRUHE	66020020	4150199000	BW BANK KARLSRUHE	MO-MI 8-15:30 UHR,DO 8-17:30 UHR,FR 8-12 UHR	poststelle@fa-rastatt.fv.bwl.de		273
8	2840	Mosbach 	Pfalzgraf-Otto-Str. 5	74821	Mosbach	06261/8070	06261/807200	74819			62000000	62001502	BBK HEILBRONN, NECKAR	62030050	5501964000	BW BANK HEILBRONN	MO-DO 08.00-16.00 UHR, DO-17.30,FR-12.00 UHR	poststelle@fa-mosbach.fv.bwl.de		274
8	2841	Pforzheim 	Moltkestr. 8	75179	Pforzheim	07231/1830	(07231)183-1111	75090			66000000	66001520	BBK KARLSRUHE	66620020	4812000000	BW BANK PFORZHEIM	MO-DO 7:30-15:30, DO bis 17:30, FR 7:30-12:00	poststelle@fa-pforzheim.fv.bwl.de		275
8	2842	Freudenstadt 	Musbacher Str. 33	72250	Freudenstadt	07441/560	07441/561011				66000000	66001510	BBK KARLSRUHE	64251060	19565	KR SPK FREUDENSTADT	MO-MI 8.00-16.00,DO 8.00-17.30,FR 8.00-12.00	poststelle@fa-freudenstadt.fv.bwl.de		276
8	2843	Schwetzingen 	Schloss	68723	Schwetzingen	06202/810	(06202) 81298	68721			67000000	67001501	BBK MANNHEIM	67250020	25008111	SPK HEIDELBERG	ZIA:MO-DO 7.30-15.30,MI-17.30,FR.7.30-12.00	poststelle@fa-schwetzingen.fv.bwl.de		277
8	2844	Sinsheim 	Bahnhofstr. 27	74889	Sinsheim	07261/6960	07261/696444	74887			67000000	67001511	BBK MANNHEIM				MO-DO 7:30-15:30, MI bis 17:30, FR 7:30-12 UHR	poststelle@fa-sinsheim.fv.bwl.de		278
8	2845	Calw 	Klosterhof 1	75365	Calw	07051/5870	07051/587111	75363			66000000	66001521	BBK KARLSRUHE	60651070	1996	SPARKASSE PFORZHEIM CALW	MO-MI 7.30-15.30,DO 7.30-17.30,FR 7.30-12.00	poststelle@fa-calw.fv.bwl.de		279
8	2846	Walldürn 	Albert-Schneider-Str. 1	74731	Walldürn	06282/7050	06282/705101	74723	74723	1162	62000000	62001509	BBK HEILBRONN, NECKAR	67450048	8102204	SPK NECKARTAL-ODENWALD	MO-MI 7.30-15.30,DO 7.30-17.30,FR 7.30-12.00	poststelle@fa-wallduern.fv.bwl.de		280
8	2847	Weinheim 	Weschnitzstr. 2	69469	Weinheim	06201/6050	(06201) 605-220/299 	69443	69443	100353	67000000	67001502	BBK MANNHEIM	67050505	63034444	SPK RHEIN NECKAR NORD	MO-MI 7.30-15.30 DO 7.30-17.30 FR 7.30-12	poststelle@fa-weinheim.fv.bwl.de		281
8	2848	Mühlacker 	Konrad-Adenauer-Platz 6	75417	Mühlacker	07041/8930	07041/893999		75415	1153	66000000	660 015 22	BBK KARLSRUHE	66650085	961 000	SPARKASSE PFORZHEIM CALW	ZIA:MO-DO 8-12:30 13:30-15:30 DO bis 17:30 FR 8-12	poststelle@fa-muehlacker.fv.bwl.de		282
8	2849	Neuenbürg 	Wildbader Str. 107	75305	Neuenbürg	07082/7990	07082/799166	75301	75301	1165	66600000	66601503	BBK PFORZHEIM	66650085	998400	SPARKASSE PFORZHEIM CALW	MO-FR 7.30-12UHR,MO-MI 13-16UHR,DO 13-18UHR	poststelle@fa-neuenbuerg.fv.bwl.de		283
8	2850	Aalen / Württemberg 	Bleichgartenstr. 17	73431	Aalen	(07361) 9578-0	(07361)9578-440	73428			63000000	614 01500	BBK ULM, DONAU	61450050	110036902	KREISSPARKASSE OSTALB	MO-MI 7.30-16.00,DO 7.30-18.00,FR 7.30-12.00	poststelle@fa-aalen.fv.bwl.de		284
8	2851	Backnang 	Stiftshof 20	71522	Backnang	07191/120	07191/12221	71522			60000000	60201501	BBK STUTTGART	60250010	244	KR SPK WAIBLINGEN	MO,DI,DO7.30-16.00MI7.30-18.00FR7.30-12.00	poststelle@fa-backnang.fv.bwl.de		285
8	2852	Bad Mergentheim 	Schloss 7	97980	Bad Mergentheim	07931/5300	07931/530228	97962	97962	1233	62000000	620 01508	BBK HEILBRONN, NECKAR	67352565	25866	SPK TAUBERFRANKEN	MO-DO 7.30-15.30,MI-17.30 UHR,FR 7.30-12 UHR	poststelle@fa-badmergentheim.fv.bwl.de		286
8	2853	Balingen 	Jakob-Beutter-Str. 4	72336	Balingen	07433/970	07433/972099	72334			64000000	653 01500	BBK REUTLINGEN	65351260	24000110	SPK ZOLLERNALB	Mo-Mi 7:45-16:00,Do 7:45-17:30,Fr 7:45-12:30	poststelle@fa-balingen.fv.bwl.de		287
8	2854	Biberach 	Bahnhofstr. 11	88400	Biberach	07351/590	07351/59202	88396			63000000	63001508	BBK ULM, DONAU	65450070	17	KR SPK BIBERACH	MO,DI,DO 8-15.30, MI 8-17.30, FR 8-12 UHR	poststelle@fa-biberach.fv.bwl.de		288
8	2855	Bietigheim-Bissingen 	Kronenbergstr. 13	74321	Bietigheim-Bissingen	07142/5900	07142/590199	74319			60000000	604 01501	BBK STUTTGART	60490150	427500001	VOLKSBANK LUDWIGSBURG	MO-MI(DO)7.30-15.30(17.30),FR 7.30-12.00 UHR	poststelle@fa-bietigheim-bissingen.fv.bwl.de		289
8	2856	Böblingen 	Talstr. 46	71034	Böblingen	(07031)13-01	07031/13-3200	71003	71003	1307	60300000	603 01500	BBK STUTTGART EH SINDELFING	60350130	220	KR SPK BOEBLINGEN	MO-MI 7.30-15.30,DO7.30-17.30,FR7.30-12.30	poststelle@fa-boeblingen.fv.bwl.de		290
8	2857	Crailsheim 	Schillerstr. 1	74564	Crailsheim	07951/4010	07951/401220	74552	74552	1252	62000000	620 01506	BBK HEILBRONN, NECKAR	62250030	282	SPARKASSE SCHWAEBISCH HALL	MO-DO:7.45-16.00,MI:-17.30,FR:7.45-12.30	poststelle@fa-crailsheim.fv.bwl.de		291
8	2858	Ehingen 	Hehlestr. 19	89584	Ehingen	07391/5080	07391/508260	89572	89572	1251	63000000	630 01502	BBK ULM, DONAU	63050000	9 300 691	SPARKASSE ULM	Mo-Mi 7.30-15.30,Do 7.30-17.30,Fr 7.30-12.00	poststelle@fa-ehingen.fv.bwl.de		292
8	2859	Esslingen 	Entengrabenstr. 11	73728	Esslingen	0711/39721	0711/3972400	73726			61100000	61101500	BBK STUTTGART EH ESSLINGEN	61150020	902139	KR SPK ESSLINGEN-NUERTINGEN	Infothek Mo-Mi 7-15.30,Do-17.30, Fr 7-12 Uhr	poststelle@fa-esslingen.fv.bwl.de		293
8	2861	Friedrichshafen 	Ehlersstr. 13	88046	Friedrichshafen	07541/7060	07541/706111	88041			63000000	65001504	BBK ULM, DONAU				MO-MI 8-15.30, DO 8-17.30, FR 8-12.30 Uhr	poststelle@fa-friedrichshafen.fv.bwl.de		294
8	2862	Geislingen 	Schillerstr. 2	73312	Geislingen	07331/220	07331/22200	73302	73302	1253	60000000	61101504	BBK STUTTGART	61050000	6007203	KR SPK GOEPPINGEN	Mo-Mi 7-15:30, Do 7-17:30,Fr 7-12	poststelle@fa-geislingen.fv.bwl.de		295
8	2863	Göppingen 	Gartenstr. 42	73033	Göppingen	07161/63-0	07161/632935		73004	420	60000000	61101503	BBK STUTTGART	61050000	1 023	KR SPK GOEPPINGEN	MO-MI.7-15.30 Uhr,DO.7-17.30 Uhr,FR.7-12 Uhr	poststelle@fa-goeppingen.fv.bwl.de		296
8	2864	Heidenheim 	Marienstr. 15	89518	Heidenheim	07321/380	07321/381528	89503	89503	1320	63000000	61401505	BBK ULM, DONAU	63250030	880433	KR SPK HEIDENHEIM	Mo-Mi 7.30-15.30 Do 7.30-17.30 Fr 7.30-12.30	poststelle@fa-heidenheim.fv.bwl.de		297
8	2865	Heilbronn 	Moltkestr. 91	74076	Heilbronn	07131/1041	07131/1043000	74064			62000000	620 01500	BBK HEILBRONN, NECKAR	62050000	123925	KR SPK HEILBRONN	Mo,Di,Do7:30-15:30,Mi7:30-17:30,Fr7:30-12:00	poststelle@fa-heilbronn.fv.bwl.de		298
8	2869	Kirchheim 	Alleenstr. 120	73230	Kirchheim	07021/5750	575258	73220	73220	1241	61100000	61101501	BBK STUTTGART EH ESSLINGEN	61150020	48317054	KR SPK ESSLINGEN-NUERTINGEN	KUNDENCENTER MO-MI 8-15.30,DO 8-17.30,FR8-12	poststelle@fa-kirchheim.fv.bwl.de		299
8	2870	Leonberg 	Schlosshof 3	71229	Leonberg	(07152) 15-1	07152/15333	71226			60000000	60301501	BBK STUTTGART	60350130	8619864	KR SPK BOEBLINGEN	MO-MI 7.30-15.30,DO 7.30-17.30,FR 7.30-12.30	poststelle@fa-leonberg.fv.bwl.de		300
8	2871	Ludwigsburg 	Alt-Württ.-Allee 40 (Neubau)	71638	Ludwigsburg	07141/180	07141/182105	71631			60000000	604 01500	BBK STUTTGART	60450050	7 759	KREISSPARKASSE LUDWIGSBURG	MO-MI 8-15.30,DO 8-18.00,FR 8-12.00	poststelle@fa-ludwigsburg.fv.bwl.de		301
8	2874	Nürtingen 	Sigmaringer Str. 15	72622	Nürtingen	07022/7090	07022/709-120	72603	72603	1309	60000000	61101502	BBK STUTTGART				MO-Mi 7.30-15.30 Do 7.30-17.30 Fr 7.30-12.00	poststelle@fa-nuertingen.fv.bwl.de		302
8	2876	Öhringen 	Haagweg 39	74613	Öhringen	07941/6040	07941/604400	74611			62000000	62001501	BBK HEILBRONN, NECKAR	62251550	40008	SPARKASSE HOHENLOHEKREIS	MO-DO 7.30-16.00UhrFR 7.30-12.00 Uhr	poststelle@fa-oehringen.fv.bwl.de		303
8	2877	Ravensburg 	Broner Platz 12	88250	Weingarten	0751/4030	403-303	88248			65000000	650 015 00	BBK ULM EH RAVENSBURG	65050110	86 500 500	KR SPK RAVENSBURG	Mo,Di,Do 8-15.30Uhr,ZIA Mi 8-17.30,Fr8-12Uhr	poststelle@fa-ravensburg.fv.bwl.de		304
8	2878	Reutlingen 	Leonhardsplatz 1	72764	Reutlingen	07121/9400	07121/9401002	72705	72705	1543	64000000	64001500	BBK REUTLINGEN	64050000	64 905	KR SPK REUTLINGEN	Mo-Mi 7-15.30, Do 7-17.30, Fr 7-12.00 Uhr	poststelle@fa-reutlingen.fv.bwl.de		305
8	2879	Riedlingen 	Kirchstr. 30	88499	Riedlingen	07371/1870	07371/1871000	88491	88491	1164	63000000	63001509	BBK ULM, DONAU	65450070	400 600	KR SPK BIBERACH	INFOST. MO-MI 7.30-15.30,DO-17.30,FR-12 UHR	poststelle@fa-riedlingen.fv.bwl.de		306
8	2880	Tauberbischofsheim 	Dr.-Burger-Str. 1	97941	Tauberbischofsheim	09341/8040	09341/804244	97933	97933	1340	62000000	620 01507	BBK HEILBRONN, NECKAR	67332551	8282661100	BW BANK TAUBERBISCHOFSHEIM	MO-MI 7.30-15.30,DO 7.30-17.30,FR 7.30-12.00	poststelle@fa-tauberbischofsheim.fv.bwl.de		307
8	2881	Bad Saulgau 	Schulstr. 5	88348	Bad Saulgau	07581/504-0	07581/504499	88341	88341	1255	65000000	650 01501	BBK ULM EH RAVENSBURG	65351050	210058	LD BK KR SPK SIGMARINGEN	MO,DO,FR 8-12,DO 13.30-15.30UHR	poststelle@fa-badsaulgau.fv.bwl.de		308
8	2882	Schorndorf 	Johann-Philipp-Palm-Str. 28	73614	Schorndorf	07181/6010	07181/601499	73603	73603	1320	60000000	60201502	BBK STUTTGART	60250010	5014008	KR SPK WAIBLINGEN	MO,DI,DO 8-15.30,MI 8-17.30,FR 8-12.00	poststelle@fa-schorndorf.fv.bwl.de		309
8	2883	Schwäbisch Gmünd 	Augustinerstr. 6	73525	Schwäbisch Gmünd	(07171) 602-0	07171/602266	73522			63000000	61401501	BBK ULM, DONAU	61450050	440066604	KREISSPARKASSE OSTALB	MO,DI,DO 8-15.30 MI 8-17.30 FR 8-12.00 UHR	poststelle@fa-schwaebischgmuend.fv.bwl.de		310
8	2884	Schwäbisch Hall 	Bahnhofstr. 25	74523	Schwäbisch Hall	0791/752-0	0791/7521115	74502	74502	100260	62000000	62001503	BBK HEILBRONN, NECKAR	62250030	5070 011	SPARKASSE SCHWAEBISCH HALL	MO-MI 7.30-16.00 DO 7.30-17.30 FR 7.30-12.00	poststelle@fa-schwaebischhall.fv.bwl.de		311
8	2885	Sigmaringen 	Karlstr. 31	72488	Sigmaringen	07571/1010	07571/101300	72481	72481	1250	65300000	653 01501	BBK REUTLINGEN EH ALBSTADT	65351050	808 408	LD BK KR SPK SIGMARINGEN	MO-MI 7.45-15.30,DO 7.45-17.30,FR 7.45-12.00	poststelle@fa-sigmaringen.fv.bwl.de		312
8	2886	Tübingen 	Steinlachallee 6 - 8	72072	Tübingen	07071/7570	07071/7574500	72005	72005	1520	64000000	64001505	BBK REUTLINGEN				Mo-Do 7.30-15.30,Mi -17.30,Fr 7.30-13.00 Uhr	poststelle@fa-tuebingen.fv.bwl.de		313
8	2887	Überlingen (Bodensee) 	Mühlenstr. 28	88662	Überlingen	07551/8360	07551/836299	88660			69400000	69001501	BBK VILLINGEN-SCHWENNINGEN	69220020	6426155500	BW BANK SINGEN	Mo-Mi 8.00-15.30,Do 8.00-17.30,Fr 8.00-12.00	poststelle@fa-ueberlingen.fv.bwl.de		314
8	2888	Ulm 	Wagnerstr. 2	89077	Ulm	0731/1030	0731/103800		89008	1860	63000000	63001500	BBK ULM, DONAU	63050000	30001	SPARKASSE ULM	MO-MI 7.30-15.30,DO 7.30-17.30,FR 7.30-12.00	poststelle@fa-ulm.fv.bwl.de		315
8	2889	Bad Urach 	Graf-Eberhard-Platz 7	72574	Bad Urach	07125/1580	(07125)158-300	72562	72562	1149	64000000	640 01501	BBK REUTLINGEN	64050000	300 346	KR SPK REUTLINGEN	MO-MI 7.30-15.30 DO 7.30-17.30 FR 7.30-12.00	poststelle@fa-badurach.fv.bwl.de		316
8	2890	Waiblingen 	Fronackerstr. 77	71332	Waiblingen	07151/9550	07151/955200	71328			60000000	602 01500	BBK STUTTGART	60250010	200 398	KR SPK WAIBLINGEN	INFOTHEK MO-DO 7.30-15.30,MI-17.30,FR-12.00	poststelle@fa-waiblingen.fv.bwl.de		317
8	2891	Wangen 	Lindauer Str.37	88239	Wangen	07522/710	07522(714000)	88228	88228	1262	63000000	650 01502	BBK ULM, DONAU	65050110	218 153	KR SPK RAVENSBURG	MO-MI 8-15.30, DO 8-17.30, FR 8-12 UHR	poststelle@fa-wangen.fv.bwl.de		318
8	2892	Stuttgart IV 	Seidenstr.23	70174	Stuttgart	0711/66730	0711/66736060	70049	70049	106052	60000000	600 01503	BBK STUTTGART	60050101	2 065 854	LANDESBANK BADEN-WUERTT	MO,MI,FR 8-12,MI 13.30-16 UHR	poststelle@fa-stuttgart4.fv.bwl.de		319
8	2893	Stuttgart I 	Rotebühlplatz 30	70173	Stuttgart	0711/66730	6673 - 5010	70049	70049	106055	60000000	600 01503	BBK STUTTGART	60050101	2 065 854	LANDESBANK BADEN-WUERTT	Mo,Die,Do: 8-15.30, Mi: 8-17.30, Fr: 8-12.00	poststelle@fa-stuttgart1.fv.bwl.de		320
8	2895	Stuttgart II 	Rotebühlstr. 40	70178	Stuttgart	0711/66730	0711/66735610				60000000	60001503	BBK STUTTGART	60050101	2065854	LANDESBANK BADEN-WUERTT	MO-DO:8-15.30 FR:8-12 MI:15.30-17.30	poststelle@fa-stuttgart2.fv.bwl.de		321
8	2896	Stuttgart Zentrales Konzernprüfungsamt	Hackstr. 86	70190	Stuttgart	0711/9251-6	0711/9251706											poststelle@zbp-stuttgart.fv.bwl.de		322
8	2897	Stuttgart III 	Rotebühlplatz 30	70173	Stuttgart	0711/66730	0711/66735710		70049	106053	60000000	600 01503	BBK STUTTGART	60050101	2 065 854	LANDESBANK BADEN-WUERTT	Mo-Do:8-15.30 Mi:8-17.30 Fr:8-12.00 Uhr	poststelle@fa-stuttgart3.fv.bwl.de		323
8	2899	Stuttgart-Körpersch. 	Paulinenstr. 44	70178	Stuttgart	0711/66730	0711/66736525	70049	70049	106051	60000000	600 01503	BBK STUTTGART	60050101	2 065 854	LANDESBANK BADEN-WUERTT	MO-FR 8:00-12:00, MO-DO 13:00-15:30 Uhr	poststelle@fa-stuttgart-koerperschaften.fv.bwl.de		324
12	3046	Potsdam-Stadt 	Am Bürohochhaus 2	14478	Potsdam	0331 287-0	0331 287-1515		14429	80 03 22	16000000	16001501	BBK POTSDAM				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Potsdam-Stadt@fa.brandenburg.de		325
12	3047	Potsdam-Land 	Steinstr. 104 - 106	14480	Potsdam	0331 6469-0	0331 6469-200		14437	90 01 45	16000000	16001502	BBK POTSDAM				täglich außer Mi: 08:00-12:30 Uhr, zusätzlich Di: 14:00-17:00 Uhr	poststelle.FA-Potsdam-Land@fa.brandenburg.de		326
12	3048	Brandenburg 	Magdeburger Straße 46	14770	Brandenburg	03381 397-100	03381 397-200				16000000	16001503	BBK POTSDAM				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Brandenburg@fa.brandenburg.de		327
12	3049	Königs Wusterhausen 	Weg am Kreisgericht 9	15711	Königs Wusterhausen	03375 275-0	03375 275-103				16000000	16001505	BBK POTSDAM				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Koenigs-Wusterhausen@fa.brandenburg.de		328
12	3050	Luckenwalde 	Industriestraße 2	14943	Luckenwalde	03371 606-0	03371 606-200				16000000	16001504	BBK POTSDAM				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Luckenwalde@fa.brandenburg.de		329
12	3051	Nauen 	Ketziner Straße 3	14641	Nauen	03321 412-0	03321 412-888		14631	11 61	16000000	16001509	BBK POTSDAM				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Nauen@fa.brandenburg.de		330
12	3052	Kyritz 	Perleberger Straße 1 - 2	16866	Kyritz	033971 65-0	033971 65-200				16000000	16001507	BBK POTSDAM				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Kyritz@fa.brandenburg.de		331
12	3053	Oranienburg 	Heinrich-Grüber-Platz 3	16515	Oranienburg	03301 857-0	03301 857-334				16000000	16001508	BBK POTSDAM				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Oranienburg@fa.brandenburg.de		332
12	3054	Pritzwalk 	Freyensteiner Chaussee 10	16928	Pritzwalk	03395 757-0	03395 302110				16000000	16001506	BBK POTSDAM				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Pritzwalk@fa.brandenburg.de		333
12	3056	Cottbus 	Vom-Stein-Straße 29	3050	Cottbus	0355 4991-4100	0355 4991-4150		3004	10 04 53	18000000	18001501	BBK COTTBUS				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Cottbus@fa.brandenburg.de		334
12	3057	Calau 	Springteichallee 25	3205	Calau	03541 83-0	03541 83-100		3201	11 71	18000000	18001502	BBK COTTBUS				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Calau@fa.brandenburg.de		335
12	3058	Finsterwalde 	Leipziger Straße 61 - 67	3238	Finsterwalde	03531 54-0	03531 54-180		3231	11 50	18000000	18001503	BBK COTTBUS				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Finsterwalde@fa.brandenburg.de		336
12	3061	Frankfurt (Oder) 	Müllroser Chaussee 53	15236	Frankfurt (Oder)	0335 560-1399	0335 560-1202				17000000	17001502	BBK FRANKFURT (ODER)				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Frankfurt-Oder@fa.brandenburg.de		337
12	3062	Angermünde 	Jahnstraße 49	16278	Angermünde	03331 267-0	03331 267-200				17000000	17001500	BBK FRANKFURT (ODER)				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Angermuende@fa.brandenburg.de		338
12	3063	Fürstenwalde 	Beeskower Chaussee 12	15517	Fürstenwalde	03361 595-0	03361 2198				17000000	17001503	BBK FRANKFURT (ODER)				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Fuerstenwalde@fa.brandenburg.de		339
12	3064	Strausberg 	Prötzeler Chaussee 12 A	15344	Strausberg	03341 342-0	03341 342-127				17000000	17001504	BBK FRANKFURT (ODER)				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Strausberg@fa.brandenburg.de		340
12	3065	Eberswalde 	Tramper Chaussee 5	16225	Eberswalde	03334 66-2000	03334 66-2001				17000000	17001501	BBK FRANKFURT (ODER)				Mo, Mi, Do: 08:00-15:00 Uhr, Di: 08:00-17:00 Uhr, Fr: 08:00-13:30 Uhr	poststelle.FA-Eberswalde@fa.brandenburg.de		341
15	3101	Magdeburg I 	Tessenowstraße 10	39114	Magdeburg	0391 885-29	0391 885-1400		39014	39 62	81000000	810 015 06	BBK MAGDEBURG				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 14.00 - 18.00 Uhr	poststelle@fa-md1.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	342
15	3102	Magdeburg II 	Tessenowstraße 6	39114	Magdeburg	0391 885-12	0391 885-1000		39006	16 63	81000000	810 015 07	BBK MAGDEBURG				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 14.00 - 18.00 Uhr	poststelle@fa-md2.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	343
15	3103	Genthin 	Berliner Chaussee 29 b	39307	Genthin	03933 908-0	03933 908-499		39302	13 41	81000000	810 015 08	BBK MAGDEBURG				Mo., Di., Do., Fr.: 09.00 - 12.00 Uhr, Di.: 14.00 - 18.00 Uhr	poststelle@fa-gtn.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	344
15	3104	Halberstadt 	R.-Wagner-Straße 51	38820	Halberstadt	03941 33-0	03941 33-199		38805	15 26	81000000	268 015 01	BBK MAGDEBURG				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 14.00 - 18.00 Uhr	poststelle@fa-hbs.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	345
15	3105	Haldensleben 	Jungfernstieg 37	39340	Haldensleben	03904 482-0	03904 482-200		39332	10 02 09	81000000	810 015 10	BBK MAGDEBURG				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 13.00 - 18.00 Uhr	poststelle@fa-hdl.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	346
15	3106	Salzwedel 	Buchenallee 2	29410	Salzwedel	03901 857-0	03901 857-100		29403	21 51	81000000	810 015 05	BBK MAGDEBURG				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 14.00 - 18.00 Uhr	poststelle@fa-saw.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	347
15	3107	Staßfurt 	Atzendorfer Straße 20	39418	Staßfurt	03925 980-0	03925 980-101		39404	13 55	81000000	810 015 12	BBK MAGDEBURG				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 13.30 - 18.00 Uhr	poststelle@fa-sft.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	348
15	3108	Stendal 	Scharnhorststraße 87	39576	Stendal	03931 57-1000	03931 57-2000		39551	10 11 31	81000000	810 015 13	BBK MAGDEBURG				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 14.00 - 18.00 Uhr	poststelle@fa-sdl.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	349
15	3109	Wernigerode 	Gustav-Petri-Straße 14	38855	Wernigerode	03943 657-0	03943 657-150		38842	10 12 51	81000000	268 015 03	BBK MAGDEBURG				Mo., Di., Do., Fr.: 09.00 - 12.00 Uhr, Do.: 14.00 - 18.00 Uhr	poststelle@fa-wrg.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	350
15	3110	Halle-Süd 	Blücherstraße 1	6122	Halle	0345 6923-5	0345 6923-600	6103			80000000	800 015 02	BBK HALLE				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 14.00 - 18.00 Uhr, Do.: 14.00	poststelle@fa-ha-s.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	351
15	3111	Halle-Nord 	Blücherstraße 1	6122	Halle	0345 6924-0	0345 6924-400	6103			80000000	800 015 01	BBK HALLE				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 14.00 - 18.00 Uhr, Do.: 14.00	poststelle@fa-ha-n.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	352
15	3112	Merseburg 	Bahnhofstraße 10	6217	Merseburg	03461 282-0	03461 282-199		6203	13 51	80000000	800 015 09	BBK HALLE				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 13.00 - 18.00 Uhr	poststelle@fa-msb.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	353
15	3113	Bitterfeld 	Röhrenstraße 33	6749	Bitterfeld	03493 347-0	03493 347-247		6732	12 64	80000000	805 015 05	BBK HALLE				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 13.00 - 18.00 Uhr	poststelle@fa-btf.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	354
15	3114	Dessau 	Kühnauer Straße 166	6846	Dessau	0340 6513-0	0340 6513-403		6815	18 25	80000000	805 015 26	BBK HALLE				Mo. - Fr.: 08.00 - 12.00 Uhr, Di.: 13.00 - 18.00 Uhr	poststelle@fa-des.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	355
15	3115	Wittenberg 	Dresdener Straße 40	6886	Wittenberg	03491 430-0	03491 430-113		6872	10 02 54	80000000	805 015 07	BBK HALLE				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 13.00 - 18.00 Uhr	poststelle@fa-wbg.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	356
15	3116	Köthen 	Zeppelinstraße 15	6366	Köthen	03496 44-0	03496 44-2900		6354	14 52	80000000	805 015 06	BBK HALLE				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 14.00 - 18.00 Uhr	poststelle@fa-kot.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	357
15	3117	Quedlinburg 	Adelheidstraße 2	6484	Quedlinburg	03946 976-0	03946 976-400		6472	14 20	81000000	268 015 02	BBK MAGDEBURG				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 13.00 - 17.30 Uhr	poststelle@fa-qlb.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	358
15	3118	Eisleben 	Bahnhofsring 10 a	6295	Eisleben	03475 725-0	03475 725-109	6291			80000000	800 015 08	BBK HALLE				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 13.00 - 18.00 Uhr	poststelle@fa-eil.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	359
15	3119	Naumburg 	Oststraße 26/26 a	6618	Naumburg	03445 753-0	03445 753-999		6602	12 51	80000000	800 015 27	BBK HALLE				Mo., Di., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 13.00 - 18.00 Uhr	poststelle@fa-nbg.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	360
15	3120	Zeitz 	Friedensstraße 80	6712	Zeitz	03441 864-0	03441 864-480		6692	12 08	80000000	800 015 04	BBK HALLE				Mo., Do., Fr.: 08.00 - 12.00 Uhr, Di.: 08.00 - 18.00 Uhr	poststelle@fa-ztz.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	361
15	3121	Sangerhausen 	Alte Promenade 27	6526	Sangerhausen	03464 539-0	03464 539-539		6512	10 12 24	80000000	800 015 25	BBK HALLE				Di., Do., Fr.: 09.00 - 12.00 Uhr, Di.: 14.00 - 18.00 Uhr, Do.: 14.00 -	poststelle@fa-sgh.ofd.mf.lsa-net.de	http://www.finanzamt.sachsen-anhalt.de	362
14	3201	Dresden I 	Lauensteiner Str. 37	1277	Dresden	0351 2567-0	0351 2567-111	1264			85000000	85001502	BBK DRESDEN				Mo 8:00-15:00, Di 8:00-18:00, Mi 8:00-15:00, Do 8:00-18:00, Fr 8:00-12:00 Uhr	poststelle@fa-dresden1.smf.sachsen.de	http://www.Finanzamt-Dresden-I.de	363
14	3202	Dresden II 	Gutzkowstraße 10	1069	Dresden	0351 4655-0	0351 4655-269	1056			85000000	85001503	BBK DRESDEN				Mo - Fr 8:00-12:00 Uhr, Di 14:00-18:00, Do 14:00-18:00 Uhr	poststelle@fa-dresden2.smf.sachsen.de	http://www.Finanzamt-Dresden-II.de	364
14	3203	Dresden III 	Rabenerstr.1	1069	Dresden	0351 4691-0	0351 4717 369		1007	120641	85000000	85001504	BBK DRESDEN				Mo 8:00-15:00, Di 8:00-18:00, Mi 8:00-15:00, Do 8:00-18:00, Fr 8:00-12:00 Uhr	poststelle@fa-dresden3.smf.sachsen.de	http://www.Finanzamt-Dresden-III.de	365
14	3204	Bautzen 	Wendischer Graben 3	2625	Bautzen	03591 488-0	03591 488-888	2621			85000000	85001505	BBK DRESDEN				Mo 8:00-15:30, Di 8:00-17:00, Mi 8:00-15:30, Do 8:00-18:00, Fr 8:00-12:00 Uhr	poststelle@fa-bautzen.smf.sachsen.de	http://www.Finanzamt-Bautzen.de	366
14	3205	Bischofswerda 	Kirchstraße 25	1877	Bischofswerda	03594 754-0	03594 754-444		1871	1111	85000000	85001506	BBK DRESDEN				Mo 8:00-15:30, Di 8:00-17:00, Mi 8:00-15:30, Do 8:00-18:00, Fr 8:00-12:00 Uhr	poststelle@fa-bischofswerda.smf.sachsen.de	http://www.Finanzamt-Bischofswerda.de	367
14	3206	Freital 	Coschützer Straße 8-10	1705	Freital	0351 6478-0	0351 6478-428		1691	1560	85000000	85001507	BBK DRESDEN				Mo 8:00-15:00, Di 8:00-18:00, Mi 8:00-15:00, Do 8:00-18:00, Fr 8:00-12:00 Uhr	poststelle@fa-freital.smf.sachsen.de	http://www.Finanzamt-Freital.de	368
14	3207	Görlitz 	Sonnenstraße 7	2826	Görlitz	03581 875-0	03581 875-100		2807	300235	85000000	85001512	BBK DRESDEN				Mo 8:00-15:30, Di 8:00-18:00, Mi 8:00-15:30, Do 8:00-17:00, Fr 8:00-12:00 Uhr	poststelle@fa-goerlitz.smf.sachsen.de	http://www.Finanzamt-Goerlitz.de	369
14	3208	Löbau 	Georgewitzer Str.40	2708	Löbau	03585 455-0	03585 455-100		2701	1165	85000000	85001509	BBK DRESDEN				Mo 8:00-15:30, Di 8:00-18:00, Mi 8:00-15:30, Do 8:00-17:00, Fr 8:00-12:00 Uhr	poststelle@fa-loebau.smf.sachsen.de	http://www.Finanzamt-Loebau.de	370
14	3209	Meißen 	Hermann-Grafe-Str.30	1662	Meißen	03521 745-30	03521 745-450		1651	100151	85000000	85001508	BBK DRESDEN				Mo - Fr 8:00-12:00 Uhr Di 13:00-18:00, Do 13:00-17:00 Uhr	poststelle@fa-meissen.smf.sachsen.de	http://www.Finanzamt-Meissen.de	371
14	3210	Pirna 	Emil-Schlegel-Str. 11	1796	Pirna	03501 551-0	03501 551-201		1781	100143	85000000	85001510	BBK DRESDEN				Mo - Fr 8:00-12:00 Uhr, Di 13:30-18:00, Do 13:30-17:00 Uhr	poststelle@fa-pirna.smf.sachsen.de	http://www.Finanzamt-Pirna.de	372
14	3211	Riesa 	Stahlwerkerstr.3	1591	Riesa	03525 714-0	03525 714-133		1571	24	85000000	85001511	BBK DRESDEN				Mo 8:00-15:30, Di 8:00-18:00, Mi 8:00-15:30, Do 8:00-17:00 , Fr 8:00-12:00 Uhr	poststelle@fa-riesa.smf.sachsen.de	http://www.Finanzamt-Riesa.de	373
14	3213	Hoyerswerda 	Pforzheimer Platz 1	2977	Hoyerswerda	03571 460-0	03571 460-115		2961	1161/1162 	85000000	85001527	BBK DRESDEN				Mo 7:30-15:30, Di 7:30-17:00, Mi 7:30-13:00, Do 7:30-18:00, Fr 7:30-12:00 Uhr	poststelle@fa-hoyerswerda.smf.sachsen.de	http://www.Finanzamt-Hoyerswerda.de	374
14	3214	Chemnitz-Süd 	Paul-Bertz-Str. 1	9120	Chemnitz	0371 279-0	0371 227065	9097			87000000	87001501	BBK CHEMNITZ				Mo 8:00-16:00, Di 8:00-18:00, Mi 8:00-13:00, Do 8:00-18:00, Fr 8:00-13:00 Uhr	poststelle@fa-chemnitz-sued.smf.sachsen.de	http://www.Finanzamt-Chemnitz-Sued.de	375
14	3215	Chemnitz-Mitte 	August-Bebel-Str. 11/13	9113	Chemnitz	0371 467-0	0371 415830	9097			87000000	87001502	BBK CHEMNITZ				Mo 8:00-16:00, Di 8:00-18:00, Mi 8:00-14:00, Do 8:00-18:00, Fr 8:00-12:00 Uhr	poststelle@fa-chemnitz-mitte.smf.sachsen.de	http://www.Finanzamt-Chemnitz-Mitte.de	376
14	3216	Chemnitz-Land 	Reichenhainer Str. 31-33	9126	Chemnitz	0371 5360-0	0371 5360-317	9097			87000000	87001503	BBK CHEMNITZ				täglich 8:00-12:00, Di 13:30-17.00, Do 13:30-18:00 Uhr	poststelle@fa-chemnitz-land.smf.sachsen.de	http://www.Finanzamt-Chemnitz-Land.de	377
14	3217	Annaberg 	Magazingasse 16	9456	Annaberg-B.	03733 4270	03733 427-217		9453	100631	87000000	87001504	BBK CHEMNITZ				Mo 8:00-15:30, Di 8:00-18:00, Mi 8:00-15:30, Do 8:00-17:00, Fr 8:00-12:00 Uhr	poststelle@fa-annaberg.smf.sachsen.de	http://www.Finanzamt-Annaberg.de	378
14	3218	Schwarzenberg 	Karlsbader Str.23	8340	Schwarzenberg	03774 161-0	03774 161-100		8332	1209	87000000	87001505	BBK CHEMNITZ				Mo 8:00-15:30, Di 8:00-18:00, Mi 8:00-15:30, Do 8:00-17:00, Fr 8:00-12:00 Uhr	poststelle@fa-schwarzenberg.smf.sachsen.de	http://www.Finanzamt-Schwarzenberg.de	379
14	3219	Auerbach 	Schulstraße 3, Haus 1	8209	Auerbach	03744 824-0	03744 824-200		8202	10132	87000000	87001506	BBK CHEMNITZ				Mo 7:30-15:30, Di 7:30-18:00, Mi 7:30-12:00, Do 7:30-18:00, Fr 7:30-12:00 Uhr	poststelle@fa-aucherbach.smf.sachsen.de	http://www.Finanzamt-Auerbach.de	380
14	3220	Freiberg 	Brückenstr.1	9599	Freiberg	03731 379-0	03731 379-999	9596			87000000	87001507	BBK CHEMNITZ				Mo - Fr 7:30-12:30, Mo 13:30-15:30, Di 13:00-18:00, Mi 13:30-15:30, Do 13:00-17:00 Uhr	poststelle@fa-freiberg.smf.sachsen.de	http://www.Finanzamt-Freiberg.de	381
14	3221	Hohenstein-Ernstthal 	Schulstraße 34	9337	Hohenstein-E.	03723 745-0	03723 745-399		9332	1246	87000000	87001510	BBK CHEMNITZ				Mo - Fr 8:00-12:00, Mo 12:30-15:30, Di 12:30-18:00, Do 12:30-17:00	poststelle@fa-hohenstein-ernstthal.smf.sachsen.de	http://www.Finanzamt-Hohenstein-Ernstthal.de	382
14	3222	Mittweida 	Robert-Koch-Str. 17	9648	Mittweida	03727 987-0	03727 987-333		9641	1157	87000000	87001509	BBK CHEMNITZ				Mo 7:30-15:00, Di 7:30-18:00, Mi 7:30-13:00, Do 7:30-18:00, Fr 7:30-12:00 Uhr	poststelle@fa-mittweida.smf.sachsen.de	http://www.Finanzamt-Mittweida.de	383
14	3223	Plauen 	Europaratstraße 17	8523	Plauen	03741 10-0	03741 10-2000		8507	100384	87000000	87001512	BBK CHEMNITZ				Mo 7:30-14:00, Di 7:30-18:00, Mi 7:30-14:00, Do 7:30-18:00, Fr 7:30-12:00 Uhr	poststelle@fa-plauen.smf.sachsen.de	http://www.Finanzamt-Plauen.de	384
14	3224	Stollberg 	HOHENSTEINER STRASSE 54	9366	Stollberg	037296 522-0	037296 522-199		9361	1107	87000000	87001508	BBK CHEMNITZ				Mo 7:30-15:30, Di 7:30-17:00, Mi 7:30-13:00, Do 7:30-18:00, Fr 7:30-12:00 Uhr	poststelle@fa-stollberg.smf.sachsen.de	http://www.Finanzamt-Stollberg.de	385
14	3226	Zwickau-Stadt 	Dr.-Friedrichs-Ring 21	8056	Zwickau	0375 3529-0	0375 3529-444		8070	100452	87000000	87001513	BBK CHEMNITZ				Mo 7:30-15:30, Di 7:30-18:00, Mi 7:30-12:00, Do 7:30-18:00, Fr 7:30-12:00 Uhr	poststelle@fa-zwickau-stadt.smf.sachsen.de	http://www.Finanzamt-Zwickau-Stadt.de	386
14	3227	Zwickau-Land 	Äußere Schneeberger Str. 62	8056	Zwickau	0375 4440-0	0375 4440-222		8067	100150	87000000	87001514	BBK CHEMNITZ				Mo 8:00-15:30, Di 8:00-18:00, Mi 8:00-15:30, Do 8:00-17:00, Fr 8:00-12:00 Uhr	poststelle@fa-zwickau-land.smf.sachsen.de	http://www.Finanzamt-Zwickau-Land.de	387
14	3228	Zschopau 	August-Bebel-Str.17	9405	Zschopau	03725 293-0	03725 293-111		9402	58	87000000	87001515	BBK CHEMNITZ				Mo7:30-12:00/13:00-16:30,Di 7:30-12:00/13:00-18:00Mi u. Fr 7:30-13:00, Do 7:30-12:00/13:00-18:00 Uhr	poststelle@fa-zschopau.smf.sachsen.de	http://www.Finanzamt-Zschopau.de	388
14	3230	Leipzig I 	Wilhelm-Liebknecht-Platz 3/4	4105	Leipzig	0341 559-0	0341 559-1540		4001	100105	86000000	86001501	BBK LEIPZIG				Mo 7:30-14:00, Di 7:30-18:00, Mi 7:30-14:00, Do 7:30-18:00, Fr 7:30-12:00 Uhr	poststelle@fa-leipzig1.smf.sachsen.de	http://www.Finanzamt-Leipzig-I.de	389
14	3231	Leipzig II 	Erich-Weinert-Str. 20	4105	Leipzig	0341 559-0	0341 559-2505		4001	100145	86000000	86001502	BBK LEIPZIG				Mo 7:30-14:00, Di 7:30-18:00, Mi 7:30-14:00, Do 7:30-18:00, Fr 7:30-12:00 Uhr	poststelle@fa-leipzig2.smf.sachsen.de	http://www.Finanzamt-Leipzig-II.de	390
14	3232	Leipzig III 	Wilhelm-Liebknecht-Platz 3/4	4105	Leipzig	0341 559-0	0341 559-3640		4002	100226	86000000	86001503	BBK LEIPZIG				Mo 7:30-14:00, Di 7:30-18:00, Mi 7:30-14:00, Do 7:30-18:00, Fr 7:30-12:00 Uhr	poststelle@fa-leipzig3.smf.sachsen.de	http://www.Finanzamt-Leipzig-III.de	391
14	3235	Borna 	Brauhausstr.8	4552	Borna	03433 872-0	03433 872-255		4541	1325	86000000	86001509	BBK LEIPZIG				Mo 8:00-15:00, Di 8:00-18:00, Mi 8:00-15:00, Do 8:00-18:00, Fr 8:00-12:00 Uhr	poststelle@fa-borna.smf.sachsen.de	http://www.Finanzamt-Borna.de	392
14	3236	Döbeln 	Burgstr.31	4720	Döbeln	03431 653-30	03431 653-444		4713	2346	86000000	86001507	BBK LEIPZIG				Mo 7:30-15:30, Di 7:30-18:00, Mi 7:30-13:00, Do 7:30-17:00, Fr 7:30-12:00 Uhr	poststelle@fa-doebeln.smf.sachsen.de	http://www.Finanzamt-Doebeln.de	393
14	3237	Eilenburg 	Walther-Rathenau-Straße 8	4838	Eilenburg	03423 660-0	03423 660-460		4831	1133	86000000	86001506	BBK LEIPZIG				Mo 8:00-16:00, Di 8:00-18:00, Mi 8:00-14:00, Do 8:00-18:00, Fr 8:00-12:00 Uhr	poststelle@fa-eilenburg.smf.sachsen.de	http://www.Finanzamt-Eilenburg.de	394
14	3238	Grimma 	Lausicker Straße 2	4668	Grimma	03437 940-0	03437 940-500		4661	1126	86000000	86001508	BBK LEIPZIG				Mo 7:30-15:00, Di 7:30-18:00, Mi 7:30-13:30, Do 7:30-17:00, Fr 7:30-12:00 Uhr	poststelle@fa-grimma.smf.sachsen.de	http://www.Finanzamt-Grimma.de	395
14	3239	Oschatz 	Dresdener Str.77	4758	Oschatz	03435 978-0	03435 978-366		4752	1265	86000000	86001511	BBK LEIPZIG				Mo 8:00-16:00, Di 8:00-17:00, Mi 8:00-15:00, Do 8:00-18:00, Fr 8:00-12:00 Uhr	poststelle@fa-oschatz.smf.sachsen.de	http://www.Finanzamt-Oschatz.de	396
13	4071	Malchin 	Schratweg 33	17139	Malchin	03994/6340	03994/634322		17131	1101	15000000	15001511	BBK NEUBRANDENBURG				Mo Di Fr 08-12 Uhr Di 13-17 Uhr und Do 13-16 UhrMittwoch geschlossen	poststelle@fa-mc.ofd-hro.de		397
13	4072	Neubrandenburg 	Neustrelitzer Str. 120	17033	Neubrandenburg	0395/380 1000	0395/3801059		17041	110164	15000000	15001518	BBK NEUBRANDENBURG				Mo Di Do Fr 08-12 Uhr und Di 13.00-17.30 Uhr Mittwoch geschlossen	poststelle@fa-nb.ofd-hro.de		398
13	4074	Pasewalk 	Torgelower Str. 32	17309	Pasewalk	(03973) 224-0	03973/2241199		17301	1102	15000000	15001512	BBK NEUBRANDENBURG				Mo bis Fr 09.00-12.00 Uhr Di 14.00-18.00 Uhr	poststelle@fa-pw.ofd-hro.de		399
13	4075	Waren 	Einsteinstr. 15	17192	Waren (Müritz)	03991/1740	(03991)174499		17183	3154	15000000	15001515	BBK NEUBRANDENBURG				Mo-Mi 08.00-16.00 Uhr Do 08.00-18.00 Uhr Fr 08.-13.00 Uhr	poststelle@fa-wrn.ofd-hro.de		400
13	4079	Rostock 	Möllner Str. 13	18109	Rostock	(0381)7000-0	(0381)7000444		18071	201062	13000000	13001508	BBK ROSTOCK				Mo Di Fr 8.30-12.00 Di 13.30-17.00 Do 13.30-16.00	poststelle@fa-hro.ofd-hro.de		401
13	4080	Wismar 	Philosophenweg 1	23970	Wismar	03841444-0	03841/444222				14000000	14001516	BBK SCHWERIN				Mo Di Fr 08.00-12.00 Uhr Di Do 14.00-17.00 Uhr Mittwoch geschlossen	poststelle@fa-wis.ofd-hro.de		402
13	4081	Ribnitz-Damgarten 	Sandhufe 3	18311	Ribnitz-Damgarten	(03821)884-0	(03821)884140		18301	1061	13000000	13001510	BBK ROSTOCK				MO Di Mi DO 08.30-12.00 UHR DI 13.00-17.00 UHR Freitag geschlossen	poststelle@fa-rdg.ofd-hro.de		403
13	4082	Stralsund 	Lindenstraße 136	18435	Stralsund	03831/3660	(03831)366245 / 188 		18409	2241	13000000	13001513	BBK ROSTOCK				Mo Di Do Fr 08.00-12.00 Uhr Di 14.00 - 18.00 UhrMittwoch geschlossen	poststelle@fa-hst.ofd-hro.de		404
13	4083	Bergen 	Wasserstr. 15 d	18528	Bergen (Rügen)	03838/4000	03838/22217	18522	18522	1242	13000000	13001512	BBK ROSTOCK				Mo Di Do Fr 8.30-12.00 Di 13.00-18.00 Mittwoch geschlossen	poststelle@fa-brg.ofd-hro.de		405
13	4084	Greifswald 	Am Gorzberg Haus 11	17489	Greifswald	03834/5590	03834-559315/316	17462	17462	3254	15000000	15001528	BBK NEUBRANDENBURG				Mo Di Do Fr 8.30-12.00 Uhr Di 13.00-17.30 Uhr Mittwoch geschlossen	poststelle@fa-hgw.ofd-hro.de		406
13	4085	Wolgast 	Pestalozzistr. 45	17438	Wolgast	03836/254-0	03836/254300 /254100		17431	1139	15000000	15001529	BBK NEUBRANDENBURG				Mo Di Mi Do Fr 08.00-12.00 Uhr	poststelle@fa-wlg.ofd-hro.de		407
13	4086	Güstrow 	Klosterhof 1	18273	Güstrow	03843/2620	03843/262111	18271			13000000	13001501	BBK ROSTOCK				Mo-Do 09.00-12.00 Uhr Do 13.00-18.00 Uhr Freitag geschlossen	poststelle@fa-gue.ofd-hro.de		408
13	4087	Hagenow 	Steegener Chaussee 8	19230	Hagenow	03883/6700	03883 670216 /670217		19222	1242	14000000	14001504	BBK SCHWERIN				Mo Di Do Fr 08.30-12.00 Di 13.00-17.30 Mittwoch geschlossen	poststelle@fa-hgn.ofd-hro.de		409
13	4089	Parchim 	Ludwigsluster Chaussee 5	19370	Parchim	03871/4650	03871/443131		19363	1351	14000000	14001506	BBK SCHWERIN				Mo Di Mi 08.30-15.00 Uhr Do 08.30-18.00 Uhr Fr 08.30-13.00 Uhr	poststelle@fa-pch.ofd-hro.de		410
13	4090	Schwerin 	Johannes-Stelling-Str.9-11	19053	Schwerin	0385/54000	0385/5400300		19091	160131	14000000	14001502	BBK SCHWERIN				 Di Do Fr 08.30 - 12.00 Uhr Mo 13.00 - 16.00 Uhr Do 14.00	poststelle@fa-sn.ofd-hro.de		411
16	4151	Erfurt 	Mittelhäuser Str. 64f	99091	Erfurt	(0361)378-00	0361/3782800		99001	100121	82050000	3001111586	LD BK HESS-THUER GZ ERFURT				DI. 8- 12/ 13.30 -18 MI./FR. 8 - 12 UHR	poststelle@finanzamt-erfurt.thueringen.de		412
16	4152	Sömmerda 	Uhlandstrasse 3	99610	Sömmerda	03634/363-0	03634/363200		99609	100	82050000	3001111628	LD BK HESS-THUER GZ ERFURT				MO/MI/DO 8-16 UHR DI 8-18,FR 8-12 UHR	poststelle@finanzamt-soemmerda.thueringen.de		413
16	4153	Weimar 	Jenaer Str.2a	99425	Weimar	03643/5500	(03643)903811		99421	3676	82050000	3001111586	LD BK HESS-THUER GZ ERFURT				MO,MI,DO 8-15.30 UHR DI 8-18,FR 8-12 UHR	poststelle@finanzamt-weimar.thueringen.de		414
16	4154	Ilmenau 	Wallgraben 1	98693	Ilmenau	(03677) 861-0	03677/861111		98686	100754	82050000	3001111685	LD BK HESS-THUER GZ ERFURT				MO,MI 8-15.30 UHR, DI 8-18 UHR DO 8-16 UHR, FR 8-12 UHR	poststelle@finanzamt-ilmenau.thueringen.de		415
16	4155	Eisenach 	Ernst-Thaelmann-Str. 70	99817	Eisenach	03691/687-0	03691/687250		99804	101454	82050000	3001111586	LD BK HESS-THUER GZ ERFURT				MO-FR: 8-12 UHR, MO-MI: 13-16 UHR, DO: 13-18 UHR	poststelle@finanzamt-eisenach.thueringen.de		416
16	4156	Gotha 	Reuterstr. 2a	99867	Gotha	(03621)33-0	03621/332000		99853	100301	82050000	3001111586	LD BK HESS-THUER GZ ERFURT				MO - MI 8-15.30 UHR DO 8-18,FR 8-12 UHR	poststelle@finanzamt-gotha.thueringen.de		417
16	4157	Mühlhausen 	Martinistr. 22	99974	Mühlhausen	(03601)456-0	03601/456100		99961	1155	82050000	3001111628	LD BK HESS-THUER GZ ERFURT				MO/MI/DO 7.30-15 UHR DI.7.30-18,FR.7.30-12	poststelle@finanzamt-muehlhausen.thueringen.de		418
16	4158	Nordhausen 	Gerhart-Hauptmann-Str. 3	99734	Nordhausen	03631/427-0	03631/427174		99729	1120	82050000	3001111628	LD BK HESS-THUER GZ ERFURT				MO,DI,MI 8-12, 13.30-16 UHR DO 8-12,14-18 FR 8-12 UHR	poststelle@finanzamt-nordhausen.thueringen.de		419
16	4159	Sondershausen 	Schillerstraße 6	99706	Sondershausen	(03632)742-0	03632/742555		99702	1265	82050000	3001111628	LD BK HESS-THUER GZ ERFURT				MO/MI/DO 8-15.30 UHR DI 8-18, FR 8-12 UHR	poststelle@finanzamt-sondershausen.thueringen.de		420
16	4160	Worbis 	Bahnhofstr. 18	37339	Worbis	036074/37-0	036074/37219		37334	173	82050000	3001111628	LD BK HESS-THUER GZ ERFURT				MO-MI 7.30-15 UHR DO 7.30-18,FR 7.30-12	poststelle@finanzamt-worbis.thueringen.de		421
16	4161	Gera 	Hermann-Drechsler-Str.1	7548	Gera	0365/639-0	0365/6391491		7490	3044	82050000	3001111578	LD BK HESS-THUER GZ ERFURT				MO,MI 7.30-15 DI,DO 7.30- 18 UHR FR 7.30-12 UHR	poststelle@finanzamt-gera.thueringen.de		422
16	4162	Jena 	Leutragraben 8	7743	Jena	(03641)378-0	03641/378653		7740	500	82050000	3001111602	LD BK HESS-THUER GZ ERFURT				MO-MI 8-15.30 DO 8-18 FR 8-12.00UHR	poststelle@finanzamt-jena.thueringen.de		423
16	4163	Rudolstadt 	Mörlaer Str. 2	7407	Rudolstadt	(03672)443-0	(03672)443100		7391	100155	82050000	3001111578	LD BK HESS-THUER GZ ERFURT				MO-MI 7.30-12, 13-15 DO 7.30-12, 13-18 UHR FR 7.30-12 UHR	poststelle@finanzamt-rudolstadt.thueringen.de		424
16	4164	Greiz 	Rosa-Luxemburg-Str. 23	7973	Greiz	03661/700-0	03661/700300		7962	1365	82050000	3001111578	LD BK HESS-THUER GZ ERFURT				MO/DI/MI 8-16UHR DO 8-18,FR 8-12UHR	poststelle@finanzamt-greiz.thueringen.de		425
16	4165	Pößneck 	Gerberstr. 65	7381	Pößneck	(03647)446-0	(03647)446430		7372	1253	82050000	3001111578	LD BK HESS-THUER GZ ERFURT				MO-FR 8-12 MO,MI,DO 13-15 UHR DI 13-18 UHR	poststelle@finanzamt-poessneck.thueringen.de		426
16	4166	Altenburg 	Wenzelstr. 45	4600	Altenburg	03447/593-0	03447/593200		4582	1251	82050000	3001111511	LD BK HESS-THUER GZ ERFURT				MO,MI,DO 7.30-15.30 DI 7.30-18 UHR FR 7.30-12 UHR	poststelle@finanzamt-altenburg.thueringen.de		427
16	4168	Bad Salzungen 	August-Bebel-Str.2	36433	Bad Salzungen	(03695)668-0	03695/622496		36421	1153	82050000	3001111586	LD BK HESS-THUER GZ ERFURT				MO-MI 7.30-15 UHR DO 7.30-18,FR 7.30-12	poststelle@finanzamt-badsalzungen.thueringen.de		428
16	4169	Meiningen 	Charlottenstr. 2	98617	Meiningen	03693/461-0	(03693)461322		98606	100661	82050000	3001111610	LD BK HESS-THUER GZ ERFURT				MO-MI 7.30-15 UHR DO 7.30-18,FR 7.30-12	poststelle@finanzamt-meiningen.thueringen.de		429
16	4170	Sonneberg 	Köppelsdorfer Str.86	96515	Sonneberg	03675/884-0	03675/884254		96502	100241	82050000	3001111685	LD BK HESS-THUER GZ ERFURT				MO-MI 7.30-15.00 UHR DO 7.30-18 FR 7.30-12	poststelle@finanzamt-sonneberg.thueringen.de		430
16	4171	Suhl 	Karl-Liebknecht-Str. 4	98527	Suhl	03681/73-0	03681/733512		98490	100153	82050000	3001111685	LD BK HESS-THUER GZ ERFURT				MO - MI 8-16 UHR, DO 8-13 u. 14-18 UHR , FR 8-12 UHR	poststelle@finanzamt-suhl.thueringen.de		431
5	5101	Dinslaken 	Schillerstr. 71	46535	Dinslaken	02064/445-0	0800 10092675101		46522	100220	35000000	35201501	BBK DUISBURG	35251000	100123	SPK DINSLAKEN-VOERDE-HUENXE	Mo-Fr 08.30-12.00 Uhr,Di auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5101.fin-nrw.de	www.finanzamt-Dinslaken.de	432
5	5102	Viersen 	Eindhovener Str. 71	41751	Viersen	02162/955-0	0800 10092675102		41726	110263	31000000	31001503	BBK MOENCHENGLADBACH	32050000	59203406	SPARKASSE KREFELD	Mo-Fr 8:30 bis 12:00 Uhr,Di auch 13:30 bis 15:00 Uhr,und nach Vereinbarung	Service@FA-5102.fin-nrw.de	www.finanzamt-Viersen.de	433
5	5103	Düsseldorf-Altstadt 	Kaiserstr. 52	40479	Düsseldorf	0211/4974-0	0800 10092675103		40001	101021	30000000	30001504	BBK DUESSELDORF	30050110	10124006	ST SPK DUESSELDORF	Mo-Fr 08.30 - 12.00 Uhr,Di auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5103.fin-nrw.de	www.finanzamt-Duesseldorf-Altstadt.de	434
5	5105	Düsseldorf-Nord 	Roßstr. 68	40476	Düsseldorf	0211/4496-0	0800 10092675105		40403	300314	30000000	30001501	BBK DUESSELDORF	30050110	10124501	ST SPK DUESSELDORF	Mo-Fr 08.30-12.00 Uhr,Di auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5105.fin-nrw.de	www.finanzamt-Duesseldorf-Nord.de	435
5	5106	Düsseldorf-Süd 	Kruppstr.110- 112	40227	Düsseldorf	0211/779-9	0800 10092675106		40001	101025	30000000	30001502	BBK DUESSELDORF	30050110	10125003	ST SPK DUESSELDORF	Mo-Fr 8.30-12.00 Uhr,Di auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5106.fin-nrw.de	www.finanzamt-Duesseldorf-Sued.de	436
5	5107	Duisburg-Hamborn 	Hufstr. 25	47166	Duisburg	0203/5445-0	0800 10092675107		47142	110264	35000000	35001502	BBK DUISBURG				Mo-Fr 08.30 - 12.00 Uhr,Di auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5107.fin-nrw.de	www.finanzamt-Duisburg-Hamborn.de	437
5	5109	Duisburg-Süd 	Landfermannstr 25	47051	Duisburg	0203/3001-0	0800 10092675109		47015	101502	35000000	35001500	BBK DUISBURG	35050000	200403020	SPK DUISBURG	Mo-Fr 08:30 Uhr - 12:00 Uhr,Di auch 13:30 Uhr - 15:00 Uhr	Service@FA-5109.fin-nrw.de	www.finanzamt-Duisburg-Sued.de	438
5	5110	Essen-Nord 	Altendorfer Str. 129	45143	Essen	0201/1894-0	0800 10092675110		45011	101155	36000000	36001500	BBK ESSEN	36050105	275008	SPARKASSE ESSEN	Mo-Fr 08.30-12.00 Uhr,Di auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5110.fin-nrw.de	www.finanzamt-Essen-Nord.de	439
5	5111	Essen-Ost 	Altendorfer Str. 129	45143	Essen	0201/1894-0	0800 10092675111	45116	45012	101262	36000000	36001501	BBK ESSEN	36050105	261800	SPARKASSE ESSEN	Mo-Fr,Di	Service@FA-5111.fin-nrw.de	www.finanzamt-Essen-Ost.de	440
5	5112	Essen-Süd 	Altendorfer Str. 129	45143	Essen	0201/1894-0	0800 10092675112		45011	101145	36000000	36001502	BBK ESSEN	36050105	203000	SPARKASSE ESSEN	Mo-Fr 08.30-12.00 Uhr, Di auch 13.30-15.00 Uhr, und nach Vereinbarung	Service@FA-5112.fin-nrw.de	www.finanzamt-Essen-Sued.de	441
5	5113	Geldern 	Gelderstr 32	47608	Geldern	02831/127-0	0800 10092675113		47591	1163	32000000	32001502	BBK MOENCHENGLADBACH EH KRE	32051370	112011	SPARKASSE GELDERN	Montag - Freitag 8:30 - 12:00,Uhr,Dienstag auch 13:00 - 15:00 U,hr und nach Vereinbarung	Service@FA-5113.fin-nrw.de	www.finanzamt-Geldern.de	442
5	5114	Grevenbroich 	Erckensstr. 2	41515	Grevenbroich	02181/607-0	0800 10092675114		41486	100264	30000000	30001507	BBK DUESSELDORF	30550000	101683	SPARKASSE NEUSS	Mo-Fr 8:30-12:00 Uhr,Di auch 13:30-15:00 Uhr,und nach Vereinbarung	Service@FA-5114.fin-nrw.de	www.finanzamt-Grevenbroich.de	443
5	5115	Kempen 	Arnoldstr 13	47906	Kempen	02152/919-0	0800 10092675115		47880	100329	31000000	32001501	BBK MOENCHENGLADBACH				MO.-DO. 8.30-12.00 UHR,FREITAGS GESCHLOSSEN	Service@FA-5115.fin-nrw.de	www.finanzamt-Kempen.de	444
5	5116	Kleve 	Emmericher Str. 182	47533	Kleve	02821/803-1	0800 10092675116		47512	1251	35000000	32401501	BBK DUISBURG	32450000	5013628	SPARKASSE KLEVE	Mo - Fr 08.30 - 12.00 Uhr,Di auch 13.30 - 15.00 Uhr	Service@FA-5116.fin-nrw.de	www.finanzamt-Kleve.de	445
5	5117	Krefeld 	Grenzstr 100	47799	Krefeld	02151/854-0	0800 10092675117		47706	100665	31000000	32001500	BBK MOENCHENGLADBACH				Mo-Fr 08.30-12.00 Uhr,Di auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5117.fin-nrw.de	www.finanzamt-Krefeld.de	446
5	5119	Moers 	Unterwallstr 1	47441	Moers	02841/208-0	0800 10092675119	47439	47405	101520	35000000	35001505	BBK DUISBURG	35450000	1101000121	SPARKASSE MOERS	Montag-Freitag von 8.30-12.00,Dienstag von 13.30-15.00	Service@FA-5119.fin-nrw.de	www.finanzamt-Moers.de	447
5	5120	Mülheim an der Ruhr 	Wilhelmstr 7	45468	Mülheim an der Ruhr	0208/3001-1	0800 10092675120		45405	100551	36000000	36201500	BBK ESSEN	36250000	300007007	SPK MUELHEIM AN DER RUHR	Mo-Fr,Di auch 13:30-15:00 Uhr,und nach Vereinbarung	Service@FA-5120.fin-nrw.de	www.finanzamt-Muelheim-Ruhr.de	448
5	5121	Mönchengladbach-Mitte 	Kleiststr. 1	41061	Mönchengladbach	02161/189-0	0800 10092675121		41008	100813	31000000	31001500	BBK MOENCHENGLADBACH	31050000	8888	ST SPK MOENCHENGLADBACH	Mo - Fr,Di auch,und nach Vereinbarung	Service@FA-5121.fin-nrw.de	www.finanzamt-Moenchengladbach-Mitte.de	449
5	5122	Neuss II 	Hammfelddamm 9	41460	Neuss	02131/6656-0	0800 10092675122		41405	100502	30000000	30001509	BBK DUESSELDORF	30550000	123000	SPARKASSE NEUSS	Mo,Di,Do,Fr von 8.30-12.00,Di von 13.30-15.00	Service@FA-5122.fin-nrw.de	www.finanzamt-Neuss2.de	450
5	5123	Oberhausen-Nord 	Gymnasialstr. 16	46145	Oberhausen	0208/6499-0	0800 10092675123		46122	110220	36000000	36501501	BBK ESSEN	36550000	260125	ST SPK OBERHAUSEN	Mo-Fr 08:30-12:00 Uhr,Di auch 13:30-15:00 Uhr,und nach Vereinbarung	Service@FA-5123.fin-nrw.de	www.finanzamt-Oberhausen-Nord.de	451
5	5124	Oberhausen-Süd 	Schwartzstr. 7-9	46045	Oberhausen	0208/8504-0	0800 10092675124		46004	100447	36000000	36501500	BBK ESSEN	36550000	138156	ST SPK OBERHAUSEN	Mo - Fr 08.30 - 12.00 Uhr,Di auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5124.fin-nrw.de	www.finanzamt-Oberhausen-Sued.de	452
5	5125	Neuss I 	Schillerstr 80	41464	Neuss	02131/943-0	0800 10092675125	41456	41405	100501	30000000	30001508	BBK DUESSELDORF	30550000	129999	SPARKASSE NEUSS	Mo-Fr 08.30-12.00 Uhr,Mo auch 13.30-15.00 Uhr	Service@FA-5125.fin-nrw.de	www.finanzamt-Neuss1.de	453
5	5126	Remscheid 	Wupperstr 10	42897	Remscheid	02191/961-0	0800 10092675126		42862	110269	33000000	33001505	BBK WUPPERTAL	34050000	113001	ST SPK REMSCHEID	Mo-Fr 08.30-12.00Uhr,Di auch 13.30-15.00Uhr,und nach Vereinbarung	Service@FA-5126.fin-nrw.de	www.finanzamt-Remscheid.de	454
5	5127	Mönchengladbach-Rheydt 	Wilhelm-Strauß-Str. 50	41236	Mönchengladbach	02166/450-0	0800 10092675127		41204	200442	31000000	31001502	BBK MOENCHENGLADBACH	31050000	295600	ST SPK MOENCHENGLADBACH	MO - FR 08.30 - 12.00 Uhr,DI auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5127.fin-nrw.de	www.finanzamt-Moenchengladbach-Rheydt.de	455
5	5128	Solingen-Ost 	Goerdelerstr.24- 26	42651	Solingen	0212/282-1	0800 10092675128	42648	42609	100984	33000000	33001503	BBK WUPPERTAL	34250000	22707	ST SPK SOLINGEN	Mo.-Fr.,Mo. auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5128.fin-nrw.de	www.finanzamt-Solingen-Ost.de	456
5	5129	Solingen-West 	Merscheider Busch 23	42699	Solingen	0212/2351-0	0800 10092675129		42663	110340	33000000	33001501	BBK WUPPERTAL	34250000	130005	ST SPK SOLINGEN	MO-FR 08.30 - 12.00 Uhr,und nach Vereinbarung	Service@FA-5129.fin-nrw.de	www.finanzamt-Solingen-West.de	457
5	5130	Wesel 	Poppelbaumstr. 5-7	46483	Wesel	0281/105-0	0800 10092675130		46461	100136	35000000	35601500	BBK DUISBURG	35650000	208660	VERB SPK WESEL	Mo-Fr 08.30 - 12.00 Uhr,Di auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5130.fin-nrw.de	www.finanzamt-Wesel.de	458
5	5131	Wuppertal-Barmen 	Unterdörnen 96	42283	Wuppertal	0202/9543-0	0800 10092675131	42271	42208	200853	33000000	33001502	BBK WUPPERTAL				Mo - Fr,Do auch,und nach Vereinbarung	Service@FA-5131.fin-nrw.de	www.finanzamt-Wuppertal-Barmen.de	459
5	5132	Wuppertal-Elberfeld 	Kasinostr. 12	42103	Wuppertal	0202/489-0	0800 10092675132		42002	100209	33000000	33001500	BBK WUPPERTAL				Mo-Fr 08.30-12.00 Uhr,Do auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5132.fin-nrw.de	www.finanzamt-Wuppertal-Elberfeld.de	460
5	5133	Düsseldorf-Mitte 	Kruppstr. 110	40227	Düsseldorf	0211/779-9	0800 10092675133		40001	101024	30000000	30001505	BBK DUESSELDORF	30050110	10123008	ST SPK DUESSELDORF		Service@FA-5133.fin-nrw.de	www.finanzamt-Duesseldorf-Mitte.de	461
5	5134	Duisburg-West 	Friedrich-Ebert-Str 133	47226	Duisburg	02065/307-0	0800 10092675134		47203	141355	35000000	35001503	BBK DUISBURG				Mo - Fr 08.30 - 12.00 Uhr,Di auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5134.fin-nrw.de	www.finanzamt-Duisburg-West.de	462
5	5135	Hilden 	Neustr. 60	40721	Hilden	02103/917-0	0800 10092675135		40710	101046	30000000	30001506	BBK DUESSELDORF				Mo-Fr 08.30-12.00 Uhr,Di auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5135.fin-nrw.de	www.finanzamt-Hilden.de	463
5	5139	Velbert 	Nedderstraße 38	42549	Velbert	02051/47-0	0800 10092675139		42513	101310	33000000	33001504	BBK WUPPERTAL	33450000	26205500	SPARKASSE HRV	Mo-Fr 08.30-12.00 Uhr,Mo auch 13.30-15.00 Uhr	Service@FA-5139.fin-nrw.de	www.finanzamt-Velbert.de	464
5	5147	Düsseldorf-Mettmann 	Harkortstr. 2-4	40210	Düsseldorf	0211/3804-0	0800 10092675147		40001	101023	30000000	30001500	BBK DUESSELDORF	30050000	4051017	WESTLB DUESSELDORF	Montag bis Freitag,08.30 bis 12.00 Uhr,und nach Vereinbarung	Service@FA-5147.fin-nrw.de	www.finanzamt-Duesseldorf-Mettmann.de	465
5	5149	Rechenzentrum d. FinVew NRW 	Roßstraße 131	40476	Düsseldorf	0211/4572-0	0211/4572-302		40408	300864								Service@FA-5149.fin-nrw.de		466
5	5170	Düsseldorf I für Groß- und Konzernbetriebsprüfung	Werftstr. 16	40549	Düsseldorf	0211/56354-01	0800 10092675170		40525	270264								Service@FA-5170.fin-nrw.de		467
5	5171	Düsseldorf II für Groß- und Konzernbetriebsprüfung	Werftstr. 16	40549	Düsseldorf	0211/56354-0	0800 10092675171		40525	270264								Service@FA-5171.fin-nrw.de		468
5	5172	Essen für Groß- und Konzernbetriebsprüfung	In der Hagenbeck 64	45143	Essen	0201/6300-1	0800 10092675172		45011	101155								Service@FA-5172.fin-nrw.de		469
5	5173	Krefeld für Groß- und Konzernbetriebsprüfung	Steinstr. 137	47798	Krefeld	02151/8418-0	0800 10092675173											Service@FA-5173.fin-nrw.de		470
5	5174	Berg. Land für Groß- und Konzernbetriebsprüfung	Bendahler Str. 29	42285	Wuppertal	0202/2832-0	0800 10092675174	42271										Service@FA-5174.fin-nrw.de		471
5	5176	Mönchengladbach für Groß- und  Konzernbetriebsprüfung	Aachener Str. 114	41061	Mönchengladbach	02161/3535-0	0800 10092675176		41017	101715								Service@FA-5176.fin-nrw.de		472
5	5181	Düsseldorf f. Steuerfahndung und Steuerstrafsachen	Kruppstr.110 -112	40227	Düsseldorf	0211/779-9	0800 10092675181		40001	101024	30000000	30001502	BBK DUESSELDORF	30050110	10125003	ST SPK DUESSELDORF	Mo - Di 07.30 - 16.30 Uhr,Mi - Fr 07.30 - 16.00 Uhr	Service@FA-5181.fin-nrw.de		473
5	5182	Essen f. Steuerfahndung und Steuerstrafsachen	In der Hagenbeck 64	45143	Essen	0201/6300-1	0800 10092675182		45011	101155	36000000	36001502	BBK ESSEN	36050105	203000	SPARKASSE ESSEN		Service@FA-5182.fin-nrw.de		474
5	5183	Wuppertal f. Steuerfahndung und Steuerstrafsachen	Unterdörnen 96	42283	Wuppertal	0202/9543-0	0800 10092675183		42205	200553	33000000	33001502	BBK WUPPERTAL	33050000	135004	ST SPK WUPPERTAL		Service@FA-5183.fin-nrw.de		475
5	5201	Aachen-Innenstadt 	Mozartstr 2-10	52064	Aachen	0241/469-0	0800 10092675201		52018	101833	39000000	39001501	BBK AACHEN	39050000	26	SPARKASSE AACHEN	Mo - Fr 08.30 - 12.00 Uhr,Mo auch 13.30 -15.00 Uhr,und nach Vereinbarung	Service@FA-5201.fin-nrw.de	www.finanzamt-Aachen-Innenstadt.de	476
5	5202	Aachen-Kreis 	Beverstr 17	52066	Aachen	0241/940-0	0800 10092675202		52018	101829	39000000	39001500	BBK AACHEN	39050000	311118	SPARKASSE AACHEN	Mo.-Fr. 08.30 - 12.00 Uhr,Mo.,und nach Vereinbarung	Service@FA-5202.fin-nrw.de	www.finanzamt-Aachen-Kreis.de	477
5	5203	Bergheim 	Rathausstrasse 3	50126	Bergheim	02271/82-0	0800 10092675203		50101	1120	39500000	39501501	BBK AACHEN EH DUEREN				Mo-Fr 08:30-12:00 Uhr,Di 13:30-15:00 Uhr,und nach Vereinbarung	Service@FA-5203.fin-nrw.de	www.finanzamt-Bergheim.de	478
5	5204	Bergisch Gladbach 	Refrather Weg 35	51469	Bergisch Gladbach	02202/9342-0	0800 10092675204		51433	200380	37000000	37001508	BBK KOELN				Mo.-Fr. 8.30-12.00 Uhr	Service@FA-5204.fin-nrw.de	www.finanzamt-Bergisch-Gladbach.de	479
5	5302	Altena 	Winkelsen 11	58762	Altena	02352/917-0	0800 10092675302		58742	1253	45000000	45001501	BBK HAGEN	45851020	80020001	VER SPK PLETTENBERG	Mo,Di-Do,und nach Vereinbarung	Service@FA-5302.fin-nrw.de	www.finanzamt-Altena.de	480
5	5205	Bonn-Innenstadt 	Welschnonnenstr. 15	53111	Bonn	0228/718-0	0800 10092675205		53031	180120	38000000	38001500	BBK BONN	38050000	17079	SPARKASSE BONN	Mo-Mi 08.30-12.00 Uhr,Do 07.00-17.00 Uhr,Freitag geschlossen	Service@FA-5205.fin-nrw.de	www.finanzamt-Bonn-Innenstadt.de	481
5	5206	Bonn-Außenstadt 	Bachstr. 36	53115	Bonn	0228/7268-0	0800 10092675206		53005	1580	38000000	38001501	BBK BONN	38050000	22004	SPARKASSE BONN	Mo-Do,Do auch 13:30 bis 17:30 Uhr,Freitags geschlossen	Service@FA-5206.fin-nrw.de	www.finanzamt-Bonn-Aussenstadt.de	482
5	5207	Düren 	Goethestrasse 7	52349	Düren	02421/947-0	0800 10092675207		52306	100646	39500000	39501500	BBK AACHEN EH DUEREN	39550110	188300	SPARKASSE DUEREN	Mo-Fr 08:30 - 12:00 Uhr,Di auch 13:30 - 15:00 Uhr,und nach Vereinbarung	Service@FA-5207.fin-nrw.de	www.finanzamt-Dueren.de	483
5	5208	Erkelenz 	Südpromenade 37	41812	Erkelenz	02431/801-0	0800 10092675208		41806	1651	31000000	31001501	BBK MOENCHENGLADBACH	31251220	402800	KR SPK HEINSBERG ERKELENZ	Mo - Fr 8.30 - 12.00 Uhr,Di auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5208.fin-nrw.de	www.finanzamt-Erkelenz.de	484
5	5209	Euskirchen 	Thomas-Mann-Str. 2	53879	Euskirchen	02251/982-0	0800 10092675209		53864	1487	38000000	38001505	BBK BONN	38250110	1000330	KREISSPARKASSE EUSKIRCHEN	Mo - Fr 08.30 - 12.00 Uhr,Mo auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5209.fin-nrw.de	www.finanzamt-Euskirchen.de	485
5	5210	Geilenkirchen 	H.-Wilh.-Str 45	52511	Geilenkirchen	02451/623-0	0800 10092675210		52501	1193	39000000	39001502	BBK AACHEN	31251220	5397	KR SPK HEINSBERG ERKELENZ	Mo.-Fr. 8.30 - 12.00 Uhr,nachmittags nur tel. von,13.30 - 15.00 Uhr	Service@FA-5210.fin-nrw.de	www.finanzamt-Geilenkirchen.de	486
5	5211	Schleiden 	Kurhausstr. 7	53937	Schleiden	02444/85-0	0800 10092675211		53929	1140	38000000	38001506	BBK BONN	38250110	3200235	KREISSPARKASSE EUSKIRCHEN	Mo-Fr 08.30 - 12.00 Uhr,Di auch 13.30 - 15.00 Uhr,sowie nach Vereinbarung	Service@FA-5211.fin-nrw.de	www.finanzamt-Schleiden.de	487
5	5212	Gummersbach 	Mühlenbergweg 5	51645	Gummersbach	02261/86-0	0800 10092675212	51641			37000000	37001506	BBK KOELN				Mo - Fr 08.30-12.00 Uhr,Mo auch 13.30-15.00 Uhr	Service@FA-5212.fin-nrw.de	www.finanzamt-Gummersbach.de	488
5	5213	Jülich 	Wilhelmstr 5	52428	Jülich	02461/685-0	0800 10092675213		52403	2180	39000000	39701500	BBK AACHEN	39550110	25023	SPARKASSE DUEREN	Mo.-Fr. 08.00-12.00 Uhr,Di. 13.30-15.00 Uhr	Service@FA-5213.fin-nrw.de	www.finanzamt-Juelich.de	489
5	5214	Köln-Altstadt 	Am Weidenbach 2-4	50676	Köln	0221/2026-0	0800 10092675214		50517	250140	37000000	37001501	BBK KOELN	37050198	70052964	STADTSPARKASSE KOELN	Mo - Fr 8.30 - 12.00 Uhr,Di auch 13.00 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5214.fin-nrw.de	www.finanzamt-Koeln-Altstadt.de	490
5	5215	Köln-Mitte 	Blaubach 7	50676	Köln	0221/92400-0	0800 10092675215		50524	290208	37000000	37001505	BBK KOELN	37050198	70062963	STADTSPARKASSE KOELN	MO-FR 08.30 - 12.00 UHR	Service@FA-5215.fin-nrw.de	www.finanzamt-Koeln-Mitte.de	491
5	5216	Köln-Porz 	Klingerstr. 2-6	51143	Köln	02203/598-0	0800 10092675216		51114	900469	37000000	37001524	BBK KOELN				Mo-Fr08.30-12.00 Uhr,Di auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5216.fin-nrw.de	www.finanzamt-Koeln-Porz.de	492
5	5217	Köln-Nord 	Innere Kanalstr. 214	50670	Köln	0221/97344-0	0800 10092675217		50495	130164	37000000	37001502	BBK KOELN	37050198	70102967	STADTSPARKASSE KOELN	Mo - Fr 8.30 - 12.00 Uhr,und nach Vereinbarung	Service@FA-5217.fin-nrw.de	www.finanzamt-Koeln-Nord.de	493
5	5218	Köln-Ost 	Siegesstrasse 1	50679	Köln	0221/9805-0	0800 10092675218		50529	210340	37000000	37001503	BBK KOELN	37050198	70082961	STADTSPARKASSE KOELN	Mo-Fr 08.30-12.00 Uhr,Di auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5218.fin-nrw.de	www.finanzamt-Koeln-Ost.de	494
5	5219	Köln-Süd 	Am Weidenbach 6	50676	Köln	0221/2026-0	0800 10092675219		50517	250160	37000000	37001504	BBK KOELN	37050198	70032966	STADTSPARKASSE KOELN	Mo-Fr,Di auch 13.00-15.00 Uhr	Service@FA-5219.fin-nrw.de	www.finanzamt-Koeln-Sued.de	495
5	5220	Siegburg 	Mühlenstr 19	53721	Siegburg	02241/105-0	0800 10092675220		53703	1351	38000000	38001503	BBK BONN				Mo.-Fr. 08.30-12.00 Uhr,Mo. auch 13.30-17.00 Uhr,und nach Vereinbarung	Service@FA-5220.fin-nrw.de	www.finanzamt-Siegburg.de	496
5	5221	Wipperfürth 	Am Stauweiher 3	51688	Wipperfürth	02267/870-0	0800 10092675221		51676	1240	37000000	37001513	BBK KOELN				Mo-Fr 08.30-12.00 Uhr,Di auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5221.fin-nrw.de	www.finanzamt-Wipperfuerth.de	497
5	5222	Sankt Augustin 	Hubert-Minz-Str 10	53757	Sankt Augustin	02241/242-1	0800 10092675222		53730	1229	38000000	38001504	BBK BONN				Mo - Fr 8.30-12.00 Uhr,Di auch 13.30-15.00 Uhr	Service@FA-5222.fin-nrw.de	www.finanzamt-Sankt-Augustin.de	498
5	5223	Köln-West 	Haselbergstr 20	50931	Köln	0221/5734-0	0800 10092675223		50864	410469	37000000	37001523	BBK KOELN	37050198	70022967	STADTSPARKASSE KOELN		Service@FA-5223.fin-nrw.de	www.finanzamt-Koeln-West.de	499
5	5224	Brühl 	Kölnstr. 104	50321	Brühl	02232/703-0	0800 10092675224	50319			37000000	37001507	BBK KOELN				Mo-Fr 08.30 - 12.00,Die zusätzlich 13.30 - 15.00 ,und nach Vereinbarung	Service@FA-5224.fin-nrw.de	www.finanzamt-Bruehl.de	500
5	5225	Aachen-Außenstadt 	Beverstraße	52066	Aachen	0241/940-0	0800 10092675225		52018	101825	39000000	39001503	BBK AACHEN	39050000	1099	SPARKASSE AACHEN	Mo - Fr 08.30 - 12.00 Uhr,Mo auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5225.fin-nrw.de	www.finanzamt-Aachen-Aussenstadt.de	501
5	5230	Leverkusen 	Haus-Vorster-Str 12	51379	Leverkusen	02171/407-0	0800 10092675230	51367			37000000	37001511	BBK KOELN	37551440	118318500	SPARKASSE LEVERKUSEN	Mo-Do 8.30 - 12.00 Uhr,Di.: 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5230.fin-nrw.de	www.finanzamt-Leverkusen.de	502
5	5270	KonzBP Köln für Groß- und Konzernbetriebsprüfung	Riehler Platz 2	50668	Köln	0221/2021-0	0800 10092675270											Service@FA-5270.fin-nrw.de		503
5	5271	Aachen für Groß- und Konzernbetriebsprüfung	Beverstr. 17	52066	Aachen	0241/940-0	0800 10092675271		52017	101744								Service@FA-5271.fin-nrw.de		504
5	5272	Bonn für Groß- und Konzernbetriebsprüfung	Am Propsthof 17	53121	Bonn	0228/7223-0	0800 10092675272											Service@FA-5272.fin-nrw.de		505
5	5281	Aachen f. Steuerfahndung und Steuerstrafsachen	Beverstr 17	52066	Aachen	0241/940-0	0800 10092675281		52017	101722	39000000	39001500	BBK AACHEN	39050000	311118	SPARKASSE AACHEN		Service@FA-5281.fin-nrw.de		506
5	5282	Bonn f. Steuerfahndung und Steuerstrafsachen	Theaterstr. 1	53111	Bonn	0228/718-0	0800 10092675282				38000000	38001500	BBK BONN	38050000	17079	SPARKASSE BONN		Service@FA-5282.fin-nrw.de		507
5	5283	Köln f. Steuerfahndung und Steuerstrafsachen	Am Gleisdreieck 7- 9	50823	Köln	0221/5772-0	0800 10092675283		50774	300451	37000000	37001502	BBK KOELN	37050198	70102967	STADTSPARKASSE KOELN		Service@FA-5283.fin-nrw.de		508
5	5301	Ahaus 	Vredener Dyk 2	48683	Ahaus	02561/929-0	0800 10092675301		48662	1251	40000000	40001503	BBK MUENSTER, WESTF	40154530	51027902	SPARKASSE WESTMUENSTERLAND	Mo - Fr 08.30 - 12.00 Uhr,zudem Mo 13.30 - 15.00 Uhr,sowie Do 13.30 -	Service@FA-5301.fin-nrw.de	www.finanzamt-Ahaus.de	509
5	5303	Arnsberg 	Rumbecker Straße 36	59821	Arnsberg	02931/875-0	0800 10092675303	59818	59802	5245	41000000	46401501	BBK HAMM, WESTF	46650005	1020007	SPK ARNSBERG-SUNDERN	Mo-Mi 08.30 - 12.00 Uhr,Fr,und nach Vereinbarung	Service@FA-5303.fin-nrw.de	www.finanzamt-Arnsberg.de	510
5	5304	Beckum 	Elisabethstraße 19	59269	Beckum	02521/25-0	0800 10092675304	59267	59244	1452	41000000	41001501	BBK HAMM, WESTF	41250035	1000223	SPK BECKUM-WADERSLOH	MO-FR 08.30-12.00 UHR,MO AUCH 13.30-15.00 UHR,UND NACH VEREINBARUNG	Service@FA-5304.fin-nrw.de	www.finanzamt-Beckum.de	511
5	5305	Bielefeld-Innenstadt 	Ravensberger Straße 90	33607	Bielefeld	0521/548-0	0800 10092675305		33503	100371	48000000	48001500	BBK BIELEFELD	48050161	109	SPK BIELEFELD	Mo - Fr 8.30 - 12.00 Uhr,Di auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5305.fin-nrw.de	www.finanzamt-Bielefeld-Innenstadt.de	512
5	5306	Bochum-Mitte 	Castroper Str. 40 - 42	44791	Bochum	0234/514-0	0800 10092675306		44707	100729	43000000	43001500	BBK BOCHUM	43050001	1300011	SPARKASSE BOCHUM	Mo-Fr 08:30 - 12:00 Uhr,Di auch 13:30 - 15:00 Uhr,Individuelle Terminver-,einbarungen sind möglich	Service@FA-5306.fin-nrw.de	www.finanzamt-Bochum-Mitte.de	513
5	5307	Borken 	Nordring 184	46325	Borken	02861/938-0	0800 10092675307	46322	46302	1240	40000000	40001514	BBK MUENSTER, WESTF	40154530	51021137	SPARKASSE WESTMUENSTERLAND	Mo-Fr 8.30 - 12.00 Uhr,Mo 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5307.fin-nrw.de	www.finanzamt-Borken.de	514
5	5308	Bottrop 	Scharnhölzstraße 32	46236	Bottrop	02041/691-0	0800 10092675308		46205	100553	43000000	42401501	BBK BOCHUM	42451220	10009	SPK BOTTROP	Mo-Mi 08.00-12.00 Uhr,Do 07.30-12.00 u 13.30-15.00 ,Freitags geschlossen	Service@FA-5308.fin-nrw.de	www.finanzamt-Bottrop.de	515
5	5309	Brilon 	Steinweg 30	59929	Brilon	02961/788-0	0800 10092675309		59915	1260	48000000	47201502	BBK BIELEFELD	41651770	17004	SPK HOCHSAUERLAND BRILON	Mo - Fr 08:30 - 12:00 Uhr,Di auch 13:30 - 15:00 Uhr,und nach Vereinbarung	Service@FA-5309.fin-nrw.de	www.finanzamt-Brilon.de	516
5	5310	Bünde 	Lettow-Vorbeck-Str 2-10	32257	Bünde	05223/169-0	0800 10092675310		32216	1649	48000000	48001502	BBK BIELEFELD	49450120	210003000	SPARKASSE HERFORD		Service@FA-5310.fin-nrw.de	www.finanzamt-Buende.de	517
5	5311	Steinfurt 	Ochtruper Straße 2	48565	Steinfurt	02551/17-0	0800 10092675311	48563	48542	1260	40000000	40301500	BBK MUENSTER, WESTF				Mo-Fr 08.00-12.00 Uhr,Mo auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5311.fin-nrw.de	www.finanzamt-Steinfurt.de	518
5	5312	Coesfeld 	Friedrich-Ebert-Str. 8	48653	Coesfeld	02541/732-0	0800 10092675312		48633	1344	40000000	40001505	BBK MUENSTER, WESTF	40154530	59001644	SPARKASSE WESTMUENSTERLAND	Mo - Fr 08.30 - 12.00 Uhr,Mo auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5312.fin-nrw.de	www.finanzamt-Coesfeld.de	519
5	5313	Detmold 	Wotanstraße 8	32756	Detmold	05231/972-0	0800 10092675313	32754	32706	1664	48000000	48001504	BBK BIELEFELD	47650130	4002	SPK DETMOLD	Mo. bis Fr.,Montags,und nach Vereinbarung	Service@FA-5313.fin-nrw.de	www.finanzamt-Detmold.de	520
5	5314	Dortmund-West 	Märkische Straße 124	44141	Dortmund	0231/9581-0	0800 10092675314		44047	105041	44000000	44001500	BBK DORTMUND	44050199	301001886	SPARKASSE DORTMUND	Montags geschlossen,Di - Fr 8.30 - 12.00,Do zusätzlich 13.30 - 15.00	Service@FA-5314.fin-nrw.de	www.finanzamt-Dortmund-West.de	521
5	5315	Dortmund-Hörde 	Niederhofener Str 3	44263	Dortmund	0231/4103-0	0800 10092675315		44232	300255	44000000	44001503	BBK DORTMUND	44050199	21003468	SPARKASSE DORTMUND	Mo-Do 8.30-12.00 Uhr,und nach Vereinbarung	Service@FA-5315.fin-nrw.de	www.finanzamt-Dortmund-Hoerde.de	522
5	5316	Dortmund-Unna 	Rennweg 1	44143	Dortmund	0231/5188-1	0800 10092675316		44047	105020	44000000	44001501	BBK DORTMUND	44050199	1060600	SPARKASSE DORTMUND	Mo-Fr 08.30-12.00 Uhr,und nach Vereinbarung	Service@FA-5316.fin-nrw.de	www.finanzamt-Dortmund-Unna.de	523
5	5317	Dortmund-Ost 	Nußbaumweg 210	44143	Dortmund	0231/5188-1	0800 10092675317		44047	105039	44000000	44001502	BBK DORTMUND	44050199	301001827	SPARKASSE DORTMUND	Mo - Fr 8.30 - 12.00 Uhr,und nach Vereinbarung	Service@FA-5317.fin-nrw.de	www.finanzamt-Dortmund-Ost.de	524
5	5318	Gelsenkirchen-Nord 	Rathausplatz 1	45894	Gelsenkirchen	0209/368-1	0800 10092675318		45838	200351	43000000	42001501	BBK BOCHUM	42050001	160012007	SPARKASSE GELSENKIRCHEN	Mo-Fr 08.30-12.00 Uhr,Mo auch 13.30-15.00Uhr	Service@FA-5318.fin-nrw.de	www.finanzamt-Gelsenkirchen-Nord.de	525
5	5319	Gelsenkirchen-Süd 	Zeppelinallee 9-13	45879	Gelsenkirchen	0209/173-1	0800 10092675319		45807	100753	43000000	42001500	BBK BOCHUM	42050001	101050003	SPARKASSE GELSENKIRCHEN	Mo - Fr 08.30 - 12.00 Uhr,Mo auch 13.30 - 15.00 Uhr	Service@FA-5319.fin-nrw.de	www.finanzamt-Gelsenkirchen-Sued.de	526
5	5320	Gladbeck 	Jovyplatz 4	45964	Gladbeck	02043/270-1	0800 10092675320		45952	240	43000000	42401500	BBK BOCHUM	42450040	91	ST SPK GLADBECK	MO-FR 08.30-12.00 UHR,DO AUCH 13.30-15.00 UHR,UND NACH VEREINBARUNG	Service@FA-5320.fin-nrw.de	www.finanzamt-Gladbeck.de	527
5	5321	Hagen 	Schürmannstraße 7	58097	Hagen	02331/180-0	0800 10092675321		58041	4145	45000000	45001500	BBK HAGEN	45050001	100001580	SPARKASSE HAGEN	Mo-Fr,Mo auch 13.30-15.00 Uhr	Service@FA-5321.fin-nrw.de	www.finanzamt-Hagen.de	528
5	5322	Hamm 	Grünstraße 2	59065	Hamm	02381/918-0	0800 10092675322	59061	59004	1449	41000000	41001500	BBK HAMM, WESTF	41050095	90001	SPARKASSE HAMM	Mo-Do 8.30-12.00 Uhr,Mi auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5322.fin-nrw.de	www.finanzamt-Hamm.de	529
5	5323	Hattingen 	Rathausplatz 19	45525	Hattingen	02324/208-0	0800 10092675323		45502	800257	43000000	43001501	BBK BOCHUM				Mo-Fr,Di auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5323.fin-nrw.de	www.finanzamt-Hattingen.de	530
5	5324	Herford 	Wittekindstraße 5	32051	Herford	05221/188-0	0800 10092675324		32006	1642	48000000	48001503	BBK BIELEFELD	49450120	36004	SPARKASSE HERFORD	Mo,Di,Fr 7.30-12.00 Uhr,Do 7.30-17.00 Uhr,Mi geschlossen,und nach Vereinbarung	Service@FA-5324.fin-nrw.de	www.finanzamt-Herford.de	531
5	5325	Herne-Ost 	Markgrafenstraße 12	44623	Herne	02323/598-0	0800 10092675325		44602	101220	43000000	43001502	BBK BOCHUM	43250030	1012004	HERNER SPARKASSE	Rückfragen bitte nur,telefonisch oder nach,vorheriger Rücksprache mit,dem Bearbeiter	Service@FA-5325.fin-nrw.de	www.finanzamt-Herne-Ost.de	532
5	5326	Höxter 	Bismarckstraße 11	37671	Höxter	05271/969-0	0800 10092675326	37669	37652	100239	48000000	47201501	BBK BIELEFELD	47251550	3008521	SPK HOEXTER BRAKEL	Mo - Do,Do auch,und nach Vereinbarung	Service@FA-5326.fin-nrw.de	www.finanzamt-Hoexter.de	533
5	5327	Ibbenbüren 	Uphof 10	49477	Ibbenbüren	05451/920-0	0800 10092675327		49462	1263	40000000	40301501	BBK MUENSTER, WESTF	40351060	2469	KR SPK STEINFURT	Mo - Fr,Di auch	Service@FA-5327.fin-nrw.de	www.finanzamt-Ibbenbueren.de	534
5	5328	Iserlohn 	Zollernstraße 16	58636	Iserlohn	02371/969-0	0800 10092675328	58634	58585	1554	45000000	45001503	BBK HAGEN	44550045	44008	SPK DER STADT ISERLOHN	Mo - Do 08.30 - 12.00 Uhr,Do auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5328.fin-nrw.de	www.finanzamt-Iserlohn.de	535
5	5376	Münster für Groß- und Konzernbetriebsprüfung	Andreas-Hofer-Straße 50	48145	Münster	0251/934-2115	0800 10092675376											Service@FA-5376.fin-nrw.de		536
5	5329	Lemgo 	Engelb.-Kämpfer Str. 18	32657	Lemgo	05261/253-1	0800 10092675329		32632	240	48000000	48001505	BBK BIELEFELD	48250110	45005	SPARKASSE LEMGO	Mo - Fr,Do auch,und nach Vereinbarung	Service@FA-5329.fin-nrw.de	www.finanzamt-Lemgo.de	537
5	5330	Lippstadt 	Im Grünen Winkel 3	59555	Lippstadt	02941/982-0	0800 10092675330		59525	1580	41000000	46401505	BBK HAMM, WESTF	41650001	15008	ST SPK LIPPSTADT	Mo - Fr 08.30 - 12.00,Do zusätzlich 13.30 - 15.00	Service@FA-5330.fin-nrw.de	www.finanzamt-Lippstadt.de	538
5	5331	Lübbecke 	Bohlenstraße 102	32312	Lübbecke	05741/334-0	0800 10092675331		32292	1244	49000000	49001501	BBK MINDEN, WESTF	49050101	141	SPARKASSE MINDEN-LUEBBECKE	Mo - Fr 08.30 - 12.00 Uhr,Mo auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5331.fin-nrw.de	www.finanzamt-Luebbecke.de	539
5	5332	Lüdenscheid 	Bahnhofsallee 16	58507	Lüdenscheid	02351/155-0	0800 10092675332	58505	58465	1589	45000000	45001502	BBK HAGEN	45850005	18	SPK LUEDENSCHEID	Mo-Fr 08.30-12.00 Uhr,Do auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5332.fin-nrw.de	www.finanzamt-Luedenscheid.de	540
5	5333	Lüdinghausen 	Bahnhofstraße 32	59348	Lüdinghausen	02591/930-0	0800 10092675333		59332	1243	40000000	40001506	BBK MUENSTER, WESTF	40154530	1008	SPARKASSE WESTMUENSTERLAND	vormittags: Mo.-Fr.8.30-12.00,nachmittags: Di. 13.30-15.00	Service@FA-5333.fin-nrw.de	www.finanzamt-Luedinghausen.de	541
5	5334	Meschede 	Fritz-Honsel-Straße 4	59872	Meschede	0291/950-0	0800 10092675334		59852	1265	41000000	46401502	BBK HAMM, WESTF	46451012	13003	SPK MESCHEDE	Mo-Fr 08:30 - 12:00,und nach Vereinbarung	Service@FA-5334.fin-nrw.de	www.finanzamt-Meschede.de	542
5	5335	Minden 	Heidestraße 10	32427	Minden	0571/804-1	0800 10092675335		32380	2340	49000000	49001500	BBK MINDEN, WESTF	49050101	40018145	SPARKASSE MINDEN-LUEBBECKE	Mo - Fr 08.30 - 12.00 Uhr,Mo auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5335.fin-nrw.de	www.finanzamt-Minden.de	543
5	5336	Münster-Außenstadt 	Friedrich-Ebert-Str. 46	48153	Münster	0251/9729-0	0800 10092675336		48136	6129	40000000	40001501	BBK MUENSTER, WESTF	40050150	95031001	SPK MUENSTERLAND OST	Mo-Fr 08.30-12.00 Uhr,Mo auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5336.fin-nrw.de	www.finanzamt-Muenster-Aussenstadt.de	544
5	5337	Münster-Innenstadt 	Münzstr. 10	48143	Münster	0251/416-1	0800 10092675337		48136	6103	40000000	40001502	BBK MUENSTER, WESTF	40050150	300004	SPK MUENSTERLAND OST		Service@FA-5337.fin-nrw.de	www.finanzamt-Muenster-Innenstadt.de	545
5	5338	Olpe 	Am Gallenberg 20	57462	Olpe	02761/963-0	0800 10092675338		57443	1320	45000000	46001501	BBK HAGEN				Mo-Do 08.30 - 12.00 Uhr,Mo auch 13.30 - 15.00 Uhr,Freitag keine Sprechzeit	Service@FA-5338.fin-nrw.de	www.finanzamt-Olpe.de	546
5	5339	Paderborn 	Bahnhofstraße 28	33102	Paderborn	05251/100-0	0800 10092675339		33045	1520	48000000	47201500	BBK BIELEFELD	47250101	1001353	SPARKASSE PADERBORN		Service@FA-5339.fin-nrw.de	www.finanzamt-Paderborn.de	547
5	5340	Recklinghausen 	Westerholter Weg 2	45657	Recklinghausen	02361/583-0	0800 10092675340		45605	100553	43000000	42601500	BBK BOCHUM	42650150	90034158	SPK RECKLINGHAUSEN	Mo - Fr 08:30 bis 12:00,Mi auch 13:30 bis 15:00,und nach Vereinbarung	Service@FA-5340.fin-nrw.de	www.finanzamt-Recklinghausen.de	548
5	5341	Schwelm 	Bahnhofplatz 6	58332	Schwelm	02336/803-0	0800 10092675341		58316	340	45000000	45001520	BBK HAGEN	45451555	80002	ST SPK SCHWELM	Mo-Fr 8.30-12.00 Uhr,Mo,und nach Vereinbarung	Service@FA-5341.fin-nrw.de	www.finanzamt-Schwelm.de	549
5	5342	Siegen 	Weidenauer Straße 207	57076	Siegen	0271/4890-0	0800 10092675342		57025	210148	45000000	46001500	BBK HAGEN	46050001	1100114	SPK SIEGEN	Mo-Fr,Do auch 13:30 - 17:00 Uhr,und nach Vereinbarung	Service@FA-5342.fin-nrw.de	www.finanzamt-Siegen.de	550
5	5343	Soest 	Waisenhausstraße 11	59494	Soest	02921/351-0	0800 10092675343	59491	59473	1364	41000000	46401504	BBK HAMM, WESTF	41450075	208	SPARKASSE SOEST	Mo-Fr 0830-1200Uhr,und nach Vereinbarung	Service@FA-5343.fin-nrw.de	www.finanzamt-Soest.de	551
5	5344	Herne-West 	Edmund-Weber-Str. 210	44651	Herne	02325/696-0	0800 10092675344		44632	200262	43000000	43001503	BBK BOCHUM	43250030	17004	HERNER SPARKASSE	Mo-Fr 08.30-12.00 Uhr,Mo 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5344.fin-nrw.de	www.finanzamt-Herne-West.de	552
5	5345	Warburg 	Sternstraße 33	34414	Warburg	05641/771-0	0800 10092675345		34402	1226	48000000	47201503	BBK BIELEFELD	47251550	25005521	SPK HOEXTER BRAKEL		Service@FA-5345.fin-nrw.de	www.finanzamt-Warburg.de	553
5	5346	Warendorf 	Düsternstraße 43	48231	Warendorf	02581/924-0	0800 10092675346		48205	110361	40000000	40001504	BBK MUENSTER, WESTF	40050150	182	SPK MUENSTERLAND OST	Mo-Fr 08.30-12.00 Uhr,Do auch 13.30-15.00 Uhr,und nach Vereinbarung	Service@FA-5346.fin-nrw.de	www.finanzamt-Warendorf.de	554
5	5347	Wiedenbrück 	Hauptstraße 34	33378	Rheda-Wiedenbrück	05242/934-0	0800 10092675347	33372	33342	1429	48000000	47801500	BBK BIELEFELD	47853520	5231	KREISSPARKASSE WIEDENBRUECK	Mo - Fr 08.30 - 12.00 Uhr,Do auch 13.30 - 14.30 Uhr	Service@FA-5347.fin-nrw.de	www.finanzamt-Wiedenbrueck.de	555
5	5348	Witten 	Ruhrstraße 43	58452	Witten	02302/921-0	0800 10092675348		58404	1420	43000000	43001505	BBK BOCHUM	45250035	6007	ST SPK WITTEN	Mo - Fr 08.30 - 12.00 Uhr,Mo auch 13.30 - 15.00 Uhr,und nach Vereinbarung	Service@FA-5348.fin-nrw.de	www.finanzamt-Witten.de	556
5	5349	Bielefeld-Außenstadt 	Ravensberger Straße 125	33607	Bielefeld	0521/548-0	0800 10092675349		33503	100331	48000000	48001501	BBK BIELEFELD	48050161	180000	SPK BIELEFELD	Mo - Fr 08:30 - 12:00 Uhr,Do auch 13:30 - 15:00 Uhr,und nach Vereinbarung	Service@FA-5349.fin-nrw.de	www.finanzamt-Bielefeld-Aussenstadt.de	557
5	5350	Bochum-Süd 	Königsallee 21	44789	Bochum	0234/3337-0	0800 10092675350		44707	100764	43000000	43001504	BBK BOCHUM	43050001	1307792	SPARKASSE BOCHUM	Mo-Fr 08:30-12:00 Uhr,Di auch 13:30-15:00 Uhr	Service@FA-5350.fin-nrw.de	www.finanzamt-Bochum-Sued.de	558
5	5351	Gütersloh 	Neuenkirchener Str. 86	33332	Gütersloh	05241/3071-0	0800 10092675351		33245	1565	48000000	48001506	BBK BIELEFELD				Mo - Fr 08.30 - 12.00 Uhr,Do auch 13.30 - 15.00 Uhr	Service@FA-5351.fin-nrw.de	www.finanzamt-Guetersloh.de	559
5	5359	Marl 	Brassertstraße 1	45768	Marl	02365/516-0	0800 10092675359	45765	45744	1420	43000000	42601501	BBK BOCHUM	42650150	40020000	SPK RECKLINGHAUSEN		Service@FA-5359.fin-nrw.de	www.finanzamt-Marl.de	560
5	5371	Bielefeld für Groß- und Konzernbetriebsprüfung	Ravensberger Str. 90	33607	Bielefeld	0521/548-0	0800 10092675371		33511	101150								Service@FA-5371.fin-nrw.de		561
5	5372	Herne für Groß- und Konzernbetriebsprüfung	Hauptstr. 123	44651	Herne	02325/693-0	0800 10092675372		44636	200620								Service@FA-5372.fin-nrw.de		562
5	5373	Detmold für Groß- und Konzernbetriebsprüfung	Richthofenstrasse 94	32756	Detmold	05231/974-300	0800 10092675373		32706	1664								Service@FA-5373.fin-nrw.de		563
5	5374	Dortmund für Groß- und Konzernbetriebsprüfung	Nußbaumweg 210	44143	Dortmund	0231/5188-8953	0800 10092675374		44047	105039								Service@FA-5374.fin-nrw.de		564
5	5375	Hagen für Groß- und Konzernbetriebsprüfung	Hochstr. 43 - 45	58095	Hagen	02331/3760-0	0800 10092675375											Service@FA-5375.fin-nrw.de		565
5	5381	Bielefeld f. Steuerfahndung und Steuerstrafsachen	Ravensberger Str. 90	33607	Bielefeld	0521/548-0	0800 10092675381		33511	101173	48000000	48001500	BBK BIELEFELD	48050161	109	SPK BIELEFELD		Service@FA-5381.fin-nrw.de		566
5	5382	Bochum f. Steuerfahndung und Steuerstrafsachen	Uhlandstr. 37	44791	Bochum	0234/5878-0	0800 10092675382		44707	100768	43000000	43001500	BBK BOCHUM	43050001	1300011	SPARKASSE BOCHUM		Service@FA-5382.fin-nrw.de		567
5	5383	Hagen f. Steuerfahndung und Steuerstrafsachen	Becheltestr. 32	58089	Hagen	02331/3089-0	0800 10092675383		58041	4143	45000000	145001500	BBK HAGEN	45050001	100001580	SPARKASSE HAGEN		Service@FA-5383.fin-nrw.de		568
5	5384	Münster f. Steuerfahndung und Steuerstrafsachen	Hohenzollernring 80	48145	Münster	0251/9370-0	0800 10092675384				40000000	40001501	BBK MUENSTER, WESTF	40050150	95031001	SPK MUENSTERLAND OST		Service@FA-5384.fin-nrw.de		569
9	9101	Augsburg-Stadt Arbeitnehmerbereich	Prinzregentenpl. 2	86150	Augsburg	0821 506-01	0821 506-2222		86135	10 00 65	72000000	72001500	BBK AUGSBURG	72050000	24109	ST SPK AUGSBURG	Servicezentrum: Mo-Mi 7:30-15:00 Uhr, Do 7:30-17:30 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-a-s.bayern.de	www.finanzamt-augsburg-stadt.de	570
9	9102	Augsburg-Land 	Peutingerstr. 25	86152	Augsburg	0821 506-02	0821 506-3270	86144	86031	11 06 69	72000000	72001501	BBK AUGSBURG	72050101	8003	KR SPK AUGSBURG	Mo, Di, Do, Fr 8:00-12:00 Uhr, Mi geschlossen	poststelle@fa-a-l.bayern.de	www.finanzamt-augsburg-land.de	571
9	9103	Augsburg-Stadt 	Prinzregentenpl. 2	86150	Augsburg	0821 506-01	0821 506-2222		86135	10 00 65	72000000	72001500	BBK AUGSBURG	72050000	24109	ST SPK AUGSBURG	Servicezentrum: Mo-Mi 7:30-15:00 Uhr, Do 7:30-17:30 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-a-s.bayern.de	www.finanzamt-augsburg-stadt.de	572
9	9104	Bad Tölz -Außenstelle des Finanzamts Wolfratshausen-	Prof.-Max-Lange-Platz 2	83646	Bad Tölz	08041 8005-0	08041 8005-185		83634	1420	70000000	70001505	BBK MUENCHEN	70054306	31054	SPK BAD TOELZ-WOLFRATSHAUSE	Servicezentrum: Mo 7:30-18:00 Uhr, Di-Do 7:30-13:00 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-toel.bayern.de	www.finanzamt-bad-toelz.de	573
9	9105	Berchtesgaden 	Salzburger Str. 6	83471	Berchtesgaden	08652 960-0	08652 960-100		83461	1154	71000000	71001500	BBK MUENCHEN EH B REICHENHA	71050000	350009	SPK BERCHTESGADENER LAND	Servicezentrum: Mo-Do 7:30-13:30 Uhr (Nov-Mai Do 7:30-18:00 Uhr), Fr 7:30-12:00 Uhr	poststelle@fa-bgd.bayern.de	www.finanzamt-berchtesgaden.de	574
9	9106	Burghausen 	Tittmoninger Str. 1	84489	Burghausen	08677 8706-0	08677 8706-100		84480	1257	71000000	71001501	BBK MUENCHEN EH B REICHENHA	71051010	250001	KR SPK ALTOETTING-BURGHAUSE	Servicezentrum: Mo-Mi 7:45-15:00 Uhr Do 7:45-17:00 Uhr, Fr 7:45-12:00 Uhr	poststelle@fa-burgh.bayern.de	www.finanzamt-burghausen.de	575
9	9107	Dachau 	Bürgermeister-Zauner-Ring 2	85221	Dachau	08131 701-0	08131 701-111	85219	85202	1280	70000000	70001507	BBK MUENCHEN	70051540	908327	SPARKASSE DACHAU	Servicezentrum: Mo, Di, Do 7:30-15:00 Uhr (Nov-Mai Do 7:30-18:00 Uhr), Mi,Fr 7:30-12:00 Uhr	poststelle@fa-dah.bayern.de	www.finanzamt-dachau.de	576
9	9108	Deggendorf 	Pfleggasse 18	94469	Deggendorf	0991 384-0	0991 384-150		94453	1355	75000000	75001506	BBK REGENSBURG	74150000	380019950	SPK DEGGENDORF	Servicezentrum: Mo, Di, Do 7:45-15:00 Uhr (Jan-Mai Do 7:45-18:00 Uhr), Mi, Fr 7:45-12:00 Uhr	poststelle@fa-deg.bayern.de	www.finanzamt-deggendorf.de	577
9	9109	Dillingen 	Schloßstr. 3	89407	Dillingen	09071 507-0	09071 507-300	89401			72000000	72001503	BBK AUGSBURG	72251520	24066	KR U ST SPK DILLINGEN	Servicezentrum: Mo, Di, Mi, Fr 7:30-13:00 Uhr, Do 7:30-13:00 Uhr u. 14:00-18:00 Uhr	poststelle@fa-dlg.bayern.de	www.finanzamt-dillingen.de	578
9	9110	Dingolfing 	Obere Stadt 44	84130	Dingolfing	08731 504-0	08731 504-190		84122	1156	74300000	74301501	BBK REGENSBURG EH LANDSHUT	74351310	100017805	SPK DINGOLFING-LANDAU	Servicezentrum: Mo-Di 7:30-15:00 Uhr, Mi, Fr 7:30-12:00 Uhr, Do 7:30-17:00 Uhr	poststelle@fa-dgf.bayern.de	www.finanzamt-dingolfing.de	579
9	9111	Donauwörth -Außenstelle des Finanzamts Nördlingen-	Sallingerstr. 2	86609	Donauwörth	0906 77-0	0906 77-150	86607			72000000	72001502	BBK AUGSBURG	70010080	1632-809	POSTBANK -GIRO- MUENCHEN	Servicezentrum: Mo-Mi 7:30-13:30 Uhr, Do 7:30-18:00 Uhr, Fr 7:30 -13:00 Uhr	poststelle@fa-don.bayern.de	www.finanzamt-donauwoerth.de	580
9	9112	Ebersberg 	Schloßplatz 1-3	85560	Ebersberg	08092 267-0	08092 267-102				70000000	70001508	BBK MUENCHEN	70051805	75	KR SPK EBERSBERG	Servicezentrum: Mo-Do 7:30-13:00 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-ebe.bayern.de	www.finanzamt-ebersberg.de	581
9	9113	Eggenfelden 	Pfarrkirchner Str. 71	84307	Eggenfelden	08721 981-0	08721 981-200		84301	1160	74300000	74301502	BBK REGENSBURG EH LANDSHUT	74351430	5603	SPK ROTTAL-INN EGGENFELDEN	Servicezentrum: Mo, Di, Do 7:45-15:00 Uhr (Jan-Mai Do 7:45-17:00 Uhr), Mi, Fr 7:30-12:00 Uhr	poststelle@fa-eg.bayern.de	www.finanzamt-eggenfelden.de	582
9	9114	Erding 	Münchener Str. 31	85435	Erding	08122 188-0	08122 188-150		85422	1262	70000000	70001509	BBK MUENCHEN	70051995	8003	SPK ERDING-DORFEN	Servicezentrum: Mo-Mi 7:30-14:00 Uhr Do 7:30-18:00 Uhr, Fr 7:30 -12:00 Uhr	poststelle@fa-ed.bayern.de	www.finanzamt-erding.de	583
9	9115	Freising 	Prinz-Ludwig-Str. 26	85354	Freising	08161 493-0	08161 493-106	85350	85313	1343	70000000	70001510	BBK MUENCHEN	70021180	4001010	HYPOVEREINSBK FREISING	Servicezentrum: Mo-Di 7:30-15:00 Uhr, Mi, Fr 7:30-12:00 Uhr, Do 7:30-18:00 Uhr	poststelle@fa-fs.bayern.de	www.finanzamt-freising.de	584
9	9117	Fürstenfeldbruck 	Münchner Str.36	82256	Fürstenfeldbruck	08141 60-0	08141 60-150		82242	1261	70000000	70001511	BBK MUENCHEN	70053070	8007221	SPK FUERSTENFELDBRUCK	Servicezentrum: Mo-Mi 7:30-14:30 Uhr, Do 7:30-17:30 Uhr, Fr 7:30 -12:30 Uhr	poststelle@fa-ffb.bayern.de	www.finanzamt-fuerstenfeldbruck.de	585
9	9118	Füssen -Außenstelle des Finanzamts Kaufbeuren-	Rupprechtstr. 1	87629	Füssen	08362 5056-0	08362 5056-290		87620	1460	73300000	73301510	BBK AUGSBURG EH KEMPTEN	73350000	310500525	SPARKASSE ALLGAEU	Servicezentrum: Mo-Mi 8:00-15:00 Uhr, Do 8:00-18:00 Uhr, Fr 8:00-13:00 Uhr	poststelle@fa-fues.bayern.de	www.finanzamt-fuessen.de	586
9	9119	Garmisch-Partenkirchen 	Von-Brug-Str. 5	82467	Garmisch-Partenkirchen	08821 700-0	08821 700-111		82453	1363	70000000	70001520	BBK MUENCHEN	70350000	505	KR SPK GARMISCH-PARTENKIRCH	Servicezentrum: Mo-Mi 7:30-14:00 Uhr, Do 7:30-18:00 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-gap.bayern.de	www.finanzamt-garmisch-partenkirchen.de	587
9	9120	Bad Griesbach -Außenstelle des Finanzamts Passau-	Schloßhof 5-6	94086	Bad Griesbach	0851 504-0	0851 504-2222		94083	1222	74000000	74001500	BBK REGENSBURG EH PASSAU	74050000	16170	SPK PASSAU	Servicezentrum: Mo-Mi 7:30-14:00 Uhr, Do 7:30-17:00 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-griesb.bayern.de	www.finanzamt-bad-griesbach.de	588
9	9121	Günzburg 	Schloßpl. 4	89312	Günzburg	08221 902-0	08221 902-209		89302	1241	72000000	72001505	BBK AUGSBURG	72051840	18	SPK GUENZBURG-KRUMBACH	Servicezentrum: Mo-Di 7:45-12:30 u. 13:30-15:30, Mi, Fr 7:45-12:30, Do 7:45-12:30 u. 13:30-18:00	poststelle@fa-gz.bayern.de	www.finanzamt-guenzburg.de	589
9	9153	Passau mit Außenstellen 	Innstr. 36	94032	Passau	0851 504-0	0851 504-1410		94030	1450	74000000	740 01500	BBK REGENSBURG EH PASSAU	74050000	16170	SPK PASSAU	Servicezentrum: Mo-Mi 7:30-15:00 Uhr, Do 7:30-17:00 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-pa.bayern.de	www.finanzamt-passau.de	590
9	9123	Immenstadt -Außenstelle des Finanzamts Kempten-	Rothenfelsstr. 18	87509	Immenstadt	08323 801-0	08323 801-235		87502	1251	73300000	73301520	BBK AUGSBURG EH KEMPTEN	73350000	113464	SPARKASSE ALLGAEU	Servicezentrum: Mo-Do 7:30-14:00 Uhr (Okt-Mai Do 7:30-18:00 Uhr), Fr 7:30-12:00 Uhr	poststelle@fa-immen.bayern.de	www.finanzamt-immenstadt.de	591
9	9124	Ingolstadt 	Esplanade 38	85049	Ingolstadt	0841 311-0	0841 311-133		85019	210451	72100000	72101500	BBK MUENCHEN EH INGOLSTADT	72150000	25 080	SPARKASSE INGOLSTADT	Servicezentrum: Mo-Di 7:15-13:30, Mi 7:15-12:30, Do 7:15-17:30, Fr 7:15-12:00	poststelle@fa-in.bayern.de	www.finanzamt-ingolstadt.de	592
9	9125	Kaufbeuren 	Remboldstr. 21	87600	Kaufbeuren	08341 802-0	08341 802-221		87572	1260	73300000	73401500	BBK AUGSBURG EH KEMPTEN	73450000	25700	KR U ST SPK KAUFBEUREN	Servicezentrum: Mo-Mi 7:30-14:00 Uhr, Do 7:30-18:00 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-kf.bayern.de	www.finanzamt-kaufbeuren.de	593
9	9126	Kelheim 	Klosterstr. 1	93309	Kelheim	09441 201-0	09441 201-201		93302	1252	75000000	75001501	BBK REGENSBURG	75051565	190201301	KREISSPARKASSE KELHEIM	Servicezentrum: Mo-Mi 7:30-15:00 Uhr, Do 7:30-17:00 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-keh.bayern.de	www.finanzamt-kelheim.de	594
9	9127	Kempten (Allgäu) 	Am Stadtpark 3	87435	Kempten	0831 256-0	0831 256-260		87405	1520	73300000	73301500	BBK AUGSBURG EH KEMPTEN	73350000	117	SPARKASSE ALLGAEU	Servicezentrum: Mo-Do 7:30-14:30 Uhr (Nov-Mai Do 7:20-17:00 Uhr), Fr 7:30-12:00 Uhr	poststelle@fa-ke.bayern.de	www.finanzamt-kempten.de	595
9	9131	Landsberg 	Israel-Beker-Str. 20	86899	Landsberg	08191 332-0	08191 332-108	86896			72000000	72001504	BBK AUGSBURG	70052060	158	SPK LANDSBERG-DIESSEN	Servicezentrum: Mo-Mi 7:30-15:00 Uhr, Do 7:30-16:00 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-ll.bayern.de	www.finanzamt-landsberg.de	596
9	9132	Landshut 	Maximilianstr. 21	84028	Landshut	0871 8529-000	0871 8529-360				74300000	74301500	BBK REGENSBURG EH LANDSHUT	74350000	10111	SPK LANDSHUT	Servicezentrum: Mo-Di 8:00-15:00 Uhr, Mi, Fr 8:00-12:00 Uhr, Do 8:00-18:00 Uhr	poststelle@fa-la.bayern.de	www.finanzamt-landshut.de	597
9	9133	Laufen - Außenstelle des Finanzamts Berchtesgaden-	Rottmayrstr. 13	83410	Laufen	08682 918-0	08682 918-100		83406	1251	71000000	71001502	BBK MUENCHEN EH B REICHENHA	71050000	59998	SPK BERCHTESGADENER LAND	Servicezentrum: Mo-Do 7:30-13:30 Uhr (Nov-Mai Do 7:30-18:00 Uhr), Fr 7:30-12:00 Uhr	poststelle@fa-lauf.bayern.de	www.finanzamt-laufen.de	598
9	9134	Lindau 	Brettermarkt 4	88131	Lindau	08382 916-0	08382 916-100		88103	1320	73300000	73501500	BBK AUGSBURG EH KEMPTEN	73150000	620018333	SPK MEMMINGEN-LINDAU-MINDEL	Servicezentrum: Mo-Do 7:30-14:00 Uhr (Nov-Mai Do 7:30-18:00 Uhr), Fr 7:30-12:00 Uhr	poststelle@fa-li.bayern.de	www.finanzamt-lindau.de	599
9	9138	Memmingen 	Bodenseestr. 6	87700	Memmingen	08331 608-0	08331 608-165		87683	1345	73100000	73101500	BBK AUGSBURG EH MEMMINGEN	73150000	210005	SPK MEMMINGEN-LINDAU-MINDEL	Servicezentrum: Mo-Do 7:30-14:00 Uhr, (Nov-Mai Do 7:30-18:00 Uhr), Fr 7:30-12:00 Uhr	poststelle@fa-mm.bayern.de	www.finanzamt-memmingen.de	600
9	9139	Miesbach 	Schlierseer Str. 5	83714	Miesbach	08025 709-0	08025 709-500		83711	302	70000000	70001512	BBK MUENCHEN	71152570	4002	KR SPK MIESBACH-TEGERNSEE	Servicezentrum: Mo, Di, Mi, Fr 7:30-14:00 Uhr, Do 7:30-18:00 Uhr	poststelle@fa-mb.bayern.de	www.finanzamt-miesbach.de	601
9	9140	Mindelheim -Außenstelle des Finanzamts Memmingen-	Bahnhofstr. 16	87719	Mindelheim	08261 9912-0	08261 9912-300		87711	1165	73100000	73101502	BBK AUGSBURG EH MEMMINGEN	73150000	810004788	SPK MEMMINGEN-LINDAU-MINDEL	Servicezentrum: Mo-Mi 7:30-12:00 u. 13:30-15:30, Do 7:30-12:00 u. 13:30-17:30, Fr 7:30-12:00	poststelle@fa-mn.bayern.de	www.finanzamt-mindelheim.de	602
9	9141	Mühldorf 	Katharinenplatz 16	84453	Mühldorf	08631 616-0	08631 616-100		84445	1369	71100000	71101501	BBK MUENCHEN EH ROSENHEIM	71151020	885	KR SPK MUEHLDORF	Servicezentrum: Mo-Mi 7:30-14:00 Uhr, Do 7:30-18:00 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-mue.bayern.de	www.finanzamt-muehldorf.de	603
9	9142	München f. Körpersch. Bewertung des Grundbesitzes	Meiserstr. 4	80333	München	089 1252-0	089 1252-7777	80275	80008	20 09 26	70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Mo, Di, Do, Fr 8:00-12:00 Uhr, Mi geschlossen	poststelle@fa-m-koe.bayern.de	www.finanzamt-muenchen-koerperschaften.de	604
9	9143	München f. Körpersch. Körperschaftsteuer	Meiserstr. 4	80333	München	089 1252-0	089 1252-7777	80275	80008	20 09 26	70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Mo, Di, Do, Fr 8:00-12:00 Uhr, Mi geschlossen	poststelle@fa-m-koe.bayern.de	www.finanzamt-muenchen-koerperschaften.de	605
9	9144	München I 	Karlstr. 9-11	80333	München	089 1252-0	089 1252-1111	80276	80008	20 09 05	70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Servicezentrum Deroystr. 6: Mo-Mi 7:30-16:00, Do 7:30-18:00, Fr 7:30-12:30 (i. Ü. nach Vereinb.)	poststelle@fa-m1.bayern.de	www.finanzamt-muenchen-I.de	606
9	9145	München III 	Deroystr. 18	80335	München	089 1252-0	089 1252-3333	80301			70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Servicezentrum Deroystr. 6: Mo-Mi 7:30-16:00, Do 7:30-18:00, Fr 7:30-12:30 (i. Ü. nach Vereinb.)	poststelle@fa-m3.bayern.de	www.finanzamt-muenchen-III.de	607
9	9146	München IV 	Deroystr. 4 Aufgang I	80335	München	089 1252-0	089 1252-4000	80302			70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Servicezentrum Deroystr. 6: Mo-Mi 7:30-16:00, Do 7:30-18:00, Fr 7:30-12:30 (i. Ü. nach Vereinb.)	poststelle@fa-m4.bayern.de	www.finanzamt-muenchen-IV.de	608
9	9147	München II 	Deroystr. 20	80335	München	089 1252-0	089 1252-2222	80269			70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Servicezentrum Deroystr. 6: Mo-Mi 7:30-16:00, Do 7:30-18:00, Fr 7:30-12:30 (i. Ü. nach Vereinb.)	poststelle@fa-m2.bayern.de	www.finanzamt-muenchen-II.de	609
9	9148	München V 	Deroystr. 4 Aufgang II	80335	München	089 1252-0	089 1252-5281	80303			70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Servicezentrum Deroystr. 6: Mo-Mi 7:30-16:00, Do 7:30-18:00, Fr 7:30-12:30 (i. Ü. nach Vereinb.)	poststelle@fa-m5.bayern.de	www.finanzamt-muenchen-V.de	610
9	9149	München-Zentral Erhebung, Vollstreckung	Winzererstr. 47a	80797	München	089 3065-0	089 3065-1900	80784			70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Mo, Di, Do, Fr 8:00-12:00 Uhr, Mi geschlossen	poststelle@fa-m-zfa.bayern.de	www.finanzamt-muenchen-zentral.de	611
9	9150	Neuburg -Außenstelle des Finanzamts Schrobenhausen-	Fünfzehnerstr. 7	86633	Neuburg	08252 918-0	08252 918-222		86618	1320	72100000	72101505	BBK MUENCHEN EH INGOLSTADT	72151880	104000	ST SPK SCHROBENHAUSEN	Servicezentrum: Mo-Mi 7:30-15:00 Uhr, Do 7:30-18:00 Uhr, Fr 7:30-12:30 Uhr	poststelle@fa-nd.bayern.de	www.finanzamt-neuburg.de	612
9	9151	Neu-Ulm 	Nelsonallee 5	89231	Neu-Ulm	0731 7045-0	0731 7045-500	89229	89204	1460	63000000	63001501	BBK ULM, DONAU	73050000	430008425	SPK NEU-ULM ILLERTISSEN	Servicezentrum: Mo, Di, Mi, Fr 8:00-13:00 Uhr, Do 8:00-13:00 Uhr u. 14:00-18:00 Uhr	poststelle@fa-nu.bayern.de	www.finanzamt-neu-ulm.de	613
9	9152	Nördlingen 	Tändelmarkt 1	86720	Nördlingen	09081 215-0	09081 215-100		86715	1521	72000000	72001506	BBK AUGSBURG	72250000	111500	SPARKASSE NOERDLINGEN	Servicezentrum: Mo, Di, Mi, Fr 7:30-13:00 Uhr, Do 7:30-13:00 Uhr u. 14:00-18:00 Uhr	poststelle@fa-noe.bayern.de	www.finanzamt-noerdlingen.de	614
9	9154	Pfaffenhofen 	Schirmbeckstr. 5	85276	Pfaffenhofen a. d. Ilm	08441 77-0	08441 77-199		85265	1543	72100000	72101504	BBK MUENCHEN EH INGOLSTADT	72151650	7302	VER SPK PFAFFENHOFEN	Servicezentrum: Mo-Mi 7:30-14:30 Uhr, Do 7:30-17:30 Uhr, Fr 7:30-12:30 Uhr	poststelle@fa-paf.bayern.de	www.finanzamt-pfaffenhofen.de	615
9	9156	Rosenheim m. ASt Wasserburg 	Wittelsbacherstr. 25	83022	Rosenheim	08031 201-0	08031 201-222		83002	100255	71100000	71101500	BBK MUENCHEN EH ROSENHEIM	71150000	34462	SPK ROSENHEIM	Servicezentrum: Mo-Do 7:30-14:00 Uhr, (Okt-Mai Do 7:30-17:00 Uhr), Fr 7:30-12.00 Uhr	poststelle@fa-ro.bayern.de	www.finanzamt-rosenheim.de	616
9	9157	Grafenau 	Friedhofstr. 1	94481	Grafenau	08552 423-0	08552 423-170				75000000	75001507	BBK REGENSBURG	70010080	1621-806	POSTBANK -GIRO- MUENCHEN	Servicezentrum: Mo, Di 7:30-15:00 Uhr, Mi, Fr 7:30-12:00 Uhr, Do 7:30-18:00 Uhr	poststelle@fa-gra.bayern.de	www.finanzamt-grafenau.de	617
9	9158	Schongau - Außenstelle des Finanzamts Weilheim-Schongau -	Rentamtstr. 1	86956	Schongau	0881 184-0	0881 184-373		86951	1147	70000000	70001521	BBK MUENCHEN	70351030	20149	VER SPK WEILHEIM	Servicezentrum: Mo-Do 7:30-14:00 Uhr (Okt-Jun Do 7:30-17:30 Uhr), Fr 7:30-12:00 Uhr	poststelle@fa-sog.bayern.de	www.finanzamt-schongau.de	618
9	9159	Schrobenhausen m. ASt Neuburg  	Rot-Kreuz-Str. 2	86529	Schrobenhausen	08252 918-0	08252 918-430		86522	1269	72100000	72101505	BBK MUENCHEN EH INGOLSTADT	72151880	104000	ST SPK SCHROBENHAUSEN	Servicezentrum: Mo-Mi 7:30-15:00 Uhr, Do 7:30-18:00 Uhr, Fr 7:30-12:30 Uhr	poststelle@fa-sob.bayern.de	www.finanzamt-schrobenhausen.de	619
9	9161	Starnberg 	Schloßbergstr.	82319	Starnberg	08151 778-0	08151 778-250		82317	1251	70000000	70001513	BBK MUENCHEN	70250150	430064295	KR SPK MUENCHEN STARNBERG	Servicezentrum: Mo-Mi 7:30-15:00 Uhr, Do 7:30-18:00 Uhr, Fr 7:30-13:00 Uhr	poststelle@fa-sta.bayern.de	www.finanzamt-starnberg.de	620
9	9162	Straubing 	Fürstenstr. 9	94315	Straubing	09421 941-0	09421 941-272		94301	151	75000000	75001502	BBK REGENSBURG	74250000	240017707	SPK STRAUBING-BOGEN	Servicezentrum: Mo, Di, Mi, Fr 7:30-13:00 Uhr, Do 7:30-18:00 Uhr	poststelle@fa-sr.bayern.de	www.finanzamt-straubing.de	621
9	9163	Traunstein 	Herzog-Otto-Str. 6	83278	Traunstein	0861 701-0	0861 701-338	83276	83263	1309	71000000	71001503	BBK MUENCHEN EH B REICHENHA	71052050	7070	KR SPK TRAUNSTEIN-TROSTBERG	Servicezentrum: Mo-Do 7:30-14:00 Uhr (Okt.-Mai Do 7:30-18:00 Uhr), Fr 7:30-12:00 Uhr	poststelle@fa-ts.bayern.de	www.finanzamt-traunstein.de	622
9	9164	Viechtach -Außenstelle des Finanzamts Zwiesel-	Mönchshofstr. 27	94234	Viechtach	09922 507-0	09922 507-399		94228	1162	75000000	75001508	BBK REGENSBURG	74151450	240001008	SPARKASSE REGEN-VIECHTACH	Servicezentrum: Mo-Di 7:45-15:00 Uhr, Mi, Fr 7:45-12:00 Uhr, Do 7:45-18:00 Uhr	poststelle@fa-viech.bayern.de	www.finanzamt-viechtach.de	623
9	9166	Vilshofen -Außenstelle des Finanzamts Passau-	Kapuzinerstr. 36	94474	Vilshofen	0851 504-0	0851 504-2465				74000000	74001500	BBK REGENSBURG EH PASSAU	74050000	16170	SPK PASSAU	Servicezentrum: Mo-Mi 7:30-14:00 Uhr, Do 7:30-17:00 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-vof.bayern.de	www.finanzamt-vilshofen.de	624
9	9167	Wasserburg -Außenstelle des Finanzamts Rosenheim-	Rosenheimer Str. 16	83512	Wasserburg	08037 201-0	08037 201-150		83502	1280	71100000	71101500	BBK MUENCHEN EH ROSENHEIM	71150000	34462	SPK ROSENHEIM	Servicezentrum: Mo-Do 7:30-14:00 Uhr, (Okt-Mai Do 7:30-17:00 Uhr), Fr 7:30-12.00 Uhr	poststelle@fa-ws.bayern.de	www.finanzamt-wasserburg.de	625
9	9168	Weilheim-Schongau 	Hofstr. 23	82362	Weilheim	0881 184-0	0881 184-500		82352	1264	70000000	70001521	BBK MUENCHEN	70351030	20149	VER SPK WEILHEIM	Servicezentrum: Mo-Do 7:30-14:00 Uhr (Okt-Jun Do 7:30-17:30 Uhr), Fr 7:30-12:00 Uhr	poststelle@fa-wm.bayern.de	www.finanzamt-weilheim.de	626
9	9169	Wolfratshausen 	Heimgartenstr. 5	82515	Wolfratshausen	08171 25-0	08171 25-150		82504	1444	70000000	70001514	BBK MUENCHEN	70054306	505	SPK BAD TOELZ-WOLFRATSHAUSE	Servicezentrum: Mo-MI 7:30-14:00 Uhr, Do 7:30-17:00 Uhr, Fr 7:30-12:30 Uhr	poststelle@fa-wor.bayern.de	www.finanzamt-wolfratshausen.de	627
9	9170	Zwiesel m. ASt Viechtach 	Stadtplatz 16	94227	Zwiesel	09922 507-0	09922 507-200		94221	1262	75000000	75001508	BBK REGENSBURG	74151450	240001008	SPARKASSE REGEN-VIECHTACH	Servicezentrum: Mo-Di 7:45-15:00 Uhr, Mi, Fr 7:45-12:00 Uhr, Do 7:45-18:00 Uhr	poststelle@fa-zwi.bayern.de	www.finanzamt-zwiesel.de	628
9	9171	Eichstätt 	Residenzplatz 8	85072	Eichstätt	08421 6007-0	08421 6007-400	85071	85065	1163	72100000	72101501	BBK MUENCHEN EH INGOLSTADT	72151340	1214	SPARKASSE EICHSTAETT	Servicezentrum: Mo, Di, Mi 7:30-14:00 Uhr, Do 7:30-18:00 Uhr, Fr 7:30-12:00 Uhr	poststelle@fa-ei.bayern.de	www.finanzamt-eichstaett.de	629
9	9180	München f. Körpersch. 	Meiserstr. 4	80333	München	089 1252-0	089 1252-7777	80275	80008	20 09 26	70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Mo, Di, Do, Fr 8:00-12:00 Uhr, Mi geschlossen	poststelle@fa-m-koe.bayern.de	www.finanzamt-muenchen-koerperschaften.de	630
9	9181	München I Arbeitnehmerbereich	Karlstr. 9/11	80333	München	089 1252-0	089 1252-1111	80276	80008	20 09 05	70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Servicezentrum Deroystr. 6: Mo-Mi 7:30-16:00, Do 7:30-18:00, Fr 7:30-12:30 (i. Ü. nach Vereinb.)	Poststelle@fa-m1-BS.bayern.de	www.finanzamt-muenchen-I.de	631
9	9182	München II Arbeitnehmerbereich	Deroystr. 20	80335	München	089 1252-0	089 1252-2888	80269			70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Servicezentrum Deroystr. 6: Mo-Mi 7:30-16:00, Do 7:30-18:00, Fr 7:30-12:30 (i. Ü. nach Vereinb.)	Poststelle@fa-m2-BS.bayern.de	www.finanzamt-muenchen-II.de	632
9	9183	München III Arbeitnehmerbereich	Deroystr. 18	80335	München	089 1252-0	089 1252-3788	80301			70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Servicezentrum Deroystr. 6: Mo-Mi 7:30-16:00, Do 7:30-18:00, Fr 7:30-12:30 (i. Ü. nach Vereinb.)	Poststelle@fa-m3-BS.bayern.de	www.finanzamt-muenchen-III.de	633
9	9184	München IV Arbeitnehmerbereich	Deroystr. 4 Aufgang I	80335	München	089 1252-0	089 1252-4820	80302			70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Servicezentrum Deroystr. 6: Mo-Mi 7:30-16:00, Do 7:30-18:00, Fr 7:30-12:30 (i. Ü. nach Vereinb.)	Poststelle@fa-m4-BS.bayern.de	www.finanzamt-muenchen-IV.de	634
9	9185	München V Arbeitnehmerbereich	Deroystr. 4 Aufgang II	80335	München	089 1252-0	089 1252-5799	80303			70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Servicezentrum Deroystr. 6: Mo-Mi 7:30-16:00, Do 7:30-18:00, Fr 7:30-12:30 (i. Ü. nach Vereinb.)	Poststelle@fa-m5-BS.bayern.de	www.finanzamt-muenchen-V.de	635
9	9187	München f. Körpersch. 	Meiserstr. 4	80333	München	089 1252-0	089 1252-7777	80275	80008	20 09 26	70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Mo, Di, Do, Fr 8:00-12:00 Uhr, Mi geschlossen	poststelle@fa-m-koe.bayern.de	www.finanzamt-muenchen-koerperschaften.de	636
9	9189	München-Zentral Kraftfahrzeugsteuer	Winzererstr. 47a	80797	München	089 3065-0	089 3065-1900	80784			70050000	24962	BAYERNLB MUENCHEN	70000000	70001506	BBK MUENCHEN	Mo, Di, Do, Fr 8:00-12:00 Uhr, Mi geschlossen	poststelle@fa-m-zfa.bayern.de	www.finanzamt-muenchen-zentral.de	637
9	9201	Amberg 	Kirchensteig 2	92224	Amberg	09621 36-0	09621 36-413		92204	1452	75300000	75301503	BBK REGENSBURG EH WEIDEN	75250000	190011122	SPARKASSE AMBERG-SULZBACH	Servicezentrum: Mo, Die, Mi, Fr: 07:30 - 12:00 UhrDo: 07:30 - 17:30 Uhr	poststelle@fa-am.bayern.de	www.finanzamt-amberg.de	638
9	9202	Obernburg a. Main mit Außenstelle Amorbach	Schneeberger Str. 1	63916	Amorbach	09373 202-0	09373 202-100		63912	1160	79500000	79501502	BBK WUERZBURG EH ASCHAFFENB	79650000	620300111	SPK MILTENBERG-OBERNBURG	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-amorb.bayern.de	www.finanzamt-amorbach.de	639
9	9203	Ansbach mit Außenstellen	Mozartstr. 25	91522	Ansbach	0981 16-0	0981 16-333		91511	608	76500000	76501500	BBK NUERNBERG EH ANSBACH	76550000	215004	VER SPK ANSBACH	Servicezentrum: Mo - Mi: 08:00 - 14:00 Uhr, Do: 08:00 - 18:00 Uhr, Fr: 08:00	poststelle@fa-an.bayern.de	www.finanzamt-ansbach.de	640
9	9204	Aschaffenburg 	Auhofstr. 13	63741	Aschaffenburg	06021 492-0	06021 492-1000	63736			79500000	79501500	BBK WUERZBURG EH ASCHAFFENB	79550000	8375	SPK ASCHAFFENBURG ALZENAU	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 8:00 - 18:00 Uhr, Fr: 08:00	poststelle@fa-ab.bayern.de	www.finanzamt-aschaffenburg.de	641
9	9205	Bad Kissingen 	Bibrastr. 10	97688	Bad Kissingen	0971 8021-0	0971 8021-200		97663	1360	79300000	79301501	BBK WUERZBURG EH SCHWEINFUR	79351010	10009	SPK BAD KISSINGEN	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-kg.bayern.de	/www.finanzamt-bad-kissingen.de	642
9	9206	Bad Neustadt a.d.S. 	Meininger Str. 39	97616	Bad Neustadt	09771 9104-0	09771 9104-444	97615			79300000	79301502	BBK WUERZBURG EH SCHWEINFUR	79353090	7005	SPK BAD NEUSTADT A D SAALE	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-nes.bayern.de	www.finanzamt-bad-neustadt.de	643
9	9207	Bamberg 	Martin-Luther-Str. 1	96050	Bamberg	0951 84-0	0951 84-230	96045			77000000	77001500	BBK NUERNBERG EH BAMBERG	77050000	30700	SPK BAMBERG	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 18:00 Uhr, Fr: 08:00	poststelle@fa-ba.bayern.de	www.finanzamt-bamberg.de	644
9	9208	Bayreuth 	Maximilianstr. 12/14	95444	Bayreuth	0921 609-0	0921 609-254		95422	110361	77300000	773 01500	BBK BAYREUTH	77350110	9033333	SPARKASSE BAYREUTH	Servicezentrum: Mo - Mi: 07:30 - 14:00 Uhr, Do: 07:30 - 17:00 Uhr, Fr: 07:30	poststelle@fa-bt.bayern.de	www.finanzamt-bayreuth.de	645
9	9211	Cham mit Außenstellen 	Reberstr. 2	93413	Cham	09971 488-0	09971 488-199		93402	1253	74221170	344 755 205	HYPOVEREINSBK CHAM, OBERPF	76010085	1735-858	POSTBANK NUERNBERG	Servicezentrum: Mo - Mi: 07:30 - 15:00 Uhr, Do: 07:30 - 18:00 Uhr, Fr: 07:30	poststelle@fa-cha.bayern.de	www.finanzamt-cham.de	646
9	9212	Coburg 	Rodacher Straße 4	96450	Coburg	09561 646-0	09561 646-130		96406	1653	77000000	78301500	BBK NUERNBERG EH BAMBERG	78350000	7450	VER SPK COBURG	Servicezentrum: Mo - Fr: 08:00 - 13:00 Uhr, Do: 14:00 - 18:00 Uhr	poststelle@fa-co.bayern.de	www.finanzamt-coburg.de	647
9	9213	Dinkelsbühl - Außenstelle des  Finanzamts Ansbach -	Föhrenberggasse 30	91550	Dinkelsbühl	0981 16-0	09851 5737-607				76500000	76501500	BBK NUERNBERG EH ANSBACH	76550000	215004	VER SPK ANSBACH	Servicezentrum: Mo - Mi: 08:00 - 14:00, Do: 08:00 - 18:00 Uhr, Fr: 08:00 -	poststelle@fa-dkb.bayern.de	www.finanzamt-dinkelsbuehl.de	648
9	9214	Ebern - Außenstelle des Finanzamts Zeil -	Rittergasse 1	96104	Ebern	09524 824-0	09524 824-225				79300000	79301505	BBK WUERZBURG EH SCHWEINFUR	79351730	500900	SPK OSTUNTERFRANKEN	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-ebn.bayern.de	www.finanzamt-ebern.de	649
9	9216	Erlangen 	Schubertstr 10	91052	Erlangen	09131 121-0	09131 121-369	91051			76000000	76001507	BBK NUERNBERG	76350000	2929	ST U KR SPK ERLANGEN	Servicezentrum: Mo - Mi: 08:00 - 14:00 Uhr, Do: 08:00 - 18:00 Uhr, Fr: 08:00	poststelle@fa-er.bayern.de	www.finanzamt-erlangen.de	650
9	9217	Forchheim 	Dechant-Reuder-Str. 6	91301	Forchheim	09191 626-0	09191 626-200	91299			76000000	76001508	BBK NUERNBERG	76351040	91	SPARKASSE FORCHHEIM	Servicezentrum: Mo - Mi: 08:00 - 13:00 Uhr, Do: 08:00 - 17:30, Fr: 08:00 -	poststelle@fa-fo.bayern.de	www.finanzamt-forchheim.de	651
9	9218	Fürth 	Herrnstraße 69	90763	Fürth	0911 7435-0	0911 7435-350	90744			76000000	76201500	BBK NUERNBERG	76250000	18200	SPK FUERTH	Servicezentrum: Mo - Mi: 08:00 - 14:00 Uhr, Do: 08:00 - 18:00 Uhr, Fr: 08:00	poststelle@fa-fue.bayern.de	www.finanzamt-fuerth.de	652
9	9220	Gunzenhausen 	Hindenburgplatz 1	91710	Gunzenhausen	09831 8009-0	09831 8009-77	91709			76500000	76501502	BBK NUERNBERG EH ANSBACH	76551540	109785	VER SPK GUNZENHAUSEN	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-gun.bayern.de	www.finanzamt-gunzenhausen.de	653
9	9221	Hersbruck 	Amberger Str. 76 (Haus B)	91217	Hersbruck	09151 731-0	09151 731-200		91211	273	76000000	76001505	BBK NUERNBERG	76050101	190016618	SPARKASSE NUERNBERG	Servicezentrum: Mo - Mi: 08:00 - 15:30 Uhr, Do: 08:00 - 18:00 Uhr, Fr: 08:00	poststelle@fa-heb.bayern.de	www.finanzamt-hersbruck.de	654
9	9222	Hilpoltstein 	Spitalwinkel 3	91161	Hilpoltstein	09174 469-0	09174 469-100		91155	1180	76000000	76401520	BBK NUERNBERG	76450000	240000026	SPK MITTELFRANKEN-SUED	Servicezentrum: Mo - Fr: 08:00 - 12:30 Uhr, Do: 14:00 - 18:00 Uhr	poststelle@fa-hip.bayern.de	www.finanzamt-hilpoltstein.de	655
9	9223	Hof mit Außenstellen 	Ernst-Reuter-Str. 60	95030	Hof	09281 929-0	09281 929-1500		95012	1368	78000000	78001500	BBK BAYREUTH EH HOF	78050000	380020750	KR U ST SPK HOF	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-ho.bayern.de	www.finanzamt-hof.de	656
9	9224	Hofheim - Außenstelle des Finanzamts Zeil -	Marktplatz 1	97457	Hofheim	09524 824-0	09524 824-250				79300000	79301505	BBK WUERZBURG EH SCHWEINFUR	79351730	500900	SPK OSTUNTERFRANKEN	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-hoh.bayern.de	www.finanzamt-hofheim.de	657
9	9225	Karlstadt - Außenstelle des Finanzamts Lohr -	Gemündener Str. 3	97753	Karlstadt	09353 949-0	09353 949-2250				79000000	79001504	BBK WUERZBURG	79050000	2246	SPK MAINFRANKEN WUERZBURG	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-kar.bayern.de	www.finanzamt-karlstadt.de	658
9	9227	Kitzingen 	Moltkestr. 24	97318	Kitzingen	09321 703-0	09321 703-444		97308	660	79000000	79101500	BBK WUERZBURG	79050000	42070557	SPK MAINFRANKEN WUERZBURG	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-kt.bayern.de	www.finanzamt-kitzingen.de	659
9	9228	Kronach 	Amtsgerichtsstr. 13	96317	Kronach	09261 510-0	09261 510-199		96302	1262	77300000	77101501	BBK BAYREUTH	77151640	240006007	SPK KRONACH-LUDWIGSSTADT	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08.00 - 17:30 Uhr, Fr: 08:00	poststelle@fa-kc.bayern.de	www.finanzamt-kronach.de	660
9	9229	Kulmbach 	Georg-Hagen-Str. 17	95326	Kulmbach	09221 650-0	09221 650-283		95304	1420	77300000	77101500	BBK BAYREUTH	77150000	105445	SPARKASSE KULMBACH	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08.00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-ku.bayern.de	www.finanzamt-kulmbach.de	661
9	9230	Lichtenfels 	Kronacher Str. 39	96215	Lichtenfels	09571 764-0	09571 764-420		96206	1680	77000000	77001502	BBK NUERNBERG EH BAMBERG	77051860	2345	KR SPK LICHTENFELS	Servicezentrum: Mo - Fr: 08:00 - 13:00 Uhr, Do: 14:00 - 17:00 Uhr	poststelle@fa-lif.bayern.de	www.finanzamt-lichtenfels.de	662
9	9231	Lohr a. Main mit Außenstellen  	Rexrothstr. 14	97816	Lohr	09352 850-0	09352 850-1300		97804	1465	79000000	79001504	BBK WUERZBURG	79050000	2246	SPK MAINFRANKEN WUERZBURG	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-loh.bayern.de	www.finanzamt-lohr.de	663
9	9232	Marktheidenfeld - Außenstelle  des Finanzamts Lohr -	Ringstr. 24/26	97828	Marktheidenfeld	09391 506-0	09391 506-3299				79000000	79001504	BBK WUERZBURG	79050000	2246	SPK MAINFRANKEN WUERZBURG	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-mar.bayern.de	www.finanzamt-marktheidenfeld.de	664
9	9233	Münchberg - Außenstelle des Finanzamts Hof -	Hofer Str. 1	95213	Münchberg	09281 929-0	09281 929-3505				78000000	78001500	BBK BAYREUTH EH HOF	78050000	380020750	KR U ST SPK HOF	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-mueb.bayern.de	www.finanzamt-muenchberg.de	665
9	9234	Naila - Außenstelle des Finanzamts Hof -	Carl-Seyffert-Str. 3	95119	Naila	09281 929-0	09281 929-2506				78000000	78001500	BBK BAYREUTH EH HOF	78050000	380020750	KR U ST SPK HOF	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-nai.bayern.de	www.finanzamt-naila.de	666
9	9235	Neumarkt i.d.Opf. 	Ingolstädter Str. 3	92318	Neumarkt	09181 692-0	09181 692-1200				76000000	76001506	BBK NUERNBERG	76052080	6296	SPK NEUMARKT I D OPF-PARSBG	Servicezentrum: Mo - Do: 07:30 - 15:00 Uhr, Fr: 07:30 - 12:00 Uhr	poststelle@fa-nm.bayern.de	/www.finanzamt-neumarkt.de	667
9	9236	Neunburg v. W. - Außenstelle des Finanzamts Schwandorf -	Krankenhausstr. 6	92431	Neunburg vorm Wald	09431 382-0	09431 382-539		92428	1000	75300000	75301502	BBK REGENSBURG EH WEIDEN	75051040	380019000	SPK IM LANDKREIS SCHWANDORF	Servicezentrum: Mo-Mi: 07:30-12:30 u. 13:30-15:30,Do: 07:30-12:30 u. 13:30-17:00, Fr: 07:30-12:30 h 	poststelle@fa-nen.bayern.de	www.finanzamt-neunburg.de	668
9	9238	Nürnberg-Nord 	Kirchenweg 10	90419	Nürnberg	0911 3998-0	0911 3998-296	90340			76000000	76001502	BBK NUERNBERG	76050000	20161	BAYERNLB NUERNBERG	Servicezentrum: Mo - Mi: 08:00 - 14:00 Uhr, Do: 08:00 - 18:00 Uhr, Fr: 08:00	poststelle@fa-n-n.bayern.de	www.finanzamt-nuernberg-nord.de	669
9	9240	Nürnberg-Süd 	Sandstr. 20	90443	Nürnberg	0911 248-0	0911 248-2299/2599	90339			76000000	76001503	BBK NUERNBERG	76050101	3648043	SPARKASSE NUERNBERG	Servicezentrum: Mo - Mi: 08:00 - 14:00 Uhr, Do: 08:00 - 18:00 Uhr, Fr: 08:00	poststelle@fa-n-s.bayern.de	www.finanzamt-nuernberg-sued.de	670
9	9241	Nürnberg-Zentral 	Voigtländerstr. 7/9	90489	Nürnberg	0911 5393-0	0911 5393-2000				76000000	76001501	BBK NUERNBERG	76050101	1025008	SPARKASSE NUERNBERG	Servicezentrum: Mo - Do: 08:00 - 12:30 h, Di und Do: 13:30 - 15:00 h,	poststelle@fa-n-zfa.bayern.de	www.zentralfinanzamt-nuernberg.de	671
9	9242	Ochsenfurt - Außenstelle des Finanzamts Würzburg -	Völkstr.1	97199	Ochsenfurt	09331 904-0	09331 904-200		97196	1263	79000000	79001500	BBK WUERZBURG	79020076	801283	HYPOVEREINSBK WUERZBURG	Servicezentrum: Mo - Mi: 07:30 - 13:00 Uhr, Do: 07:30 - 17:00 uhr, Fr: 07:30	poststelle@fa-och.bayern.de	www.finanzamt-ochsenfurt.de	672
9	9244	Regensburg 	Landshuter Str. 4	93047	Regensburg	0941 5024-0	0941 5024-1199	93042			75000000	75001500	BBK REGENSBURG	75050000	111500	SPK REGENSBURG	Servicezentrum: Mo - Mi: 07:30 - 15:00 Uhr, Do: 07:30 - 17:00 Uhr, Fr: 07:30	poststelle@fa-r.bayern.de	www.finanzamt-regensburg.de	673
9	9246	Rothenburg - Außenstelle des Finanzamts Ansbach -	Ludwig-Siebert-Str. 31	91541	Rothenburg o.d.T.	0981 16-0	09861 706-511				76500000	76501500	BBK NUERNBERG EH ANSBACH	76550000	215004	VER SPK ANSBACH	Servicezentrum: Mo - Mi: 08:00 - 14:00 Uhr, Do: 08:00 - 18:00 Uhr, Fr: 08:00	poststelle@fa-rot.bayern.de	www.finanzamt-rothenburg.de	674
9	9247	Schwabach 	Theodor-Heuss-Str. 63	91126	Schwabach	09122 928-0	09122 928-100	91124			76000000	76401500	BBK NUERNBERG	76450000	55533	SPK MITTELFRANKEN-SUED	Servicezentrum: Mo - Mi: 08:00 - 13:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-sc.bayern.de	www.finanzamt-schwabach.de	675
9	9248	Schwandorf mit Außenstelle Neunburg v. W.	Friedrich-Ebert-Str.59	92421	Schwandorf	09431 382-0	09431 382-111	92419			75300000	75301502	BBK REGENSBURG EH WEIDEN	75051040	380019000	SPK IM LANDKREIS SCHWANDORF	Servicezentrum: Mo-Mi: 07:30-12:30 u. 13:30-15:30,Do: 07:30-12:30 u. 13:30-17:00, Fr: 07:30-12:30 h 	poststelle@fa-sad.bayern.de	www.finanzamt-schwandorf.de	676
9	9249	Schweinfurt 	Schrammstr. 3	97421	Schweinfurt	09721 2911-0	09721 2911-5070	97420			79300000	79301500	BBK WUERZBURG EH SCHWEINFUR	79350101	15800	KR SPK SCHWEINFURT	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-sw.bayern.de	www.finanzamt-schweinfurt.de	677
9	9250	Selb - Außenstelle des Finanzamts Wunsiedel -	Wittelsbacher Str. 8	95100	Selb	09232 607-0	09232 607-300				78000000	78101512	BBK BAYREUTH EH HOF	78055050	620006254	SPK FICHTELGEBIRGE	Servicezentrum: Mo-Mi: 07:30-12:30 u. 13:30-15:00,Do: 07:30-12:30 und 13:30-17:00, Fr: 07:30-12:00 h	poststelle@fa-sel.bayern.de	www.finanzamt-selb.de	678
9	9252	Uffenheim 	Schloßpl.	97215	Uffenheim	09842 200-0	09842 200-345		97211	1240	76500000	76501504	BBK NUERNBERG EH ANSBACH	76251020	620002006	SPK I LANDKREIS NEUSTADT	Servicezentrum: Mo-Mi: 08:00-12:00 u. 13:00-15:00,Do: 08:00-12:00 u. 13:00-17:00, Fr: 08:00-12:00 h 	poststelle@fa-uff.bayern.de	www.finanzamt-uffenheim.de	679
9	9253	Waldmünchen - Außenstelle des  Finanzamts Cham -	Bahnhofstr. 10	93449	Waldmünchen	09971 488-0	09971 488-550				74221170	344 755 205	HYPOVEREINSBK CHAM, OBERPF	76010085	1735-858	POSTBANK NUERNBERG	Servicezentrum: Mo - Mi: 07:30 - 15:00 Uhr, Do: 07:30 - 17:00 Uhr, Fr: 07:30	poststelle@fa-wuem.bayern.de	www.finanzamt-waldmuenchen.de	680
9	9254	Waldsassen 	Johannisplatz 13	95652	Waldsassen	09632 847-0	09632 847-199		95646	1329	75300000	75301511	BBK REGENSBURG EH WEIDEN	78151080	32367	SPK TIRSCHENREUTH	Servicezentrum: Mo - Fr: 07:30 - 12:30 Uhr, Mo - Mi: 13:30 - 15:30 Uhr,	poststelle@fa-wasa.bayern.de	www.finanzamt-waldsassen.de	681
9	9255	Weiden i.d.Opf. 	Schlörpl. 2 u. 4	92637	Weiden	0961 301-0	0961 32600		92604	1460	75300000	75301500	BBK REGENSBURG EH WEIDEN	75350000	172700	ST SPK WEIDEN	Servicezentrum: Mo - Fr: 07:30 - 12:30 Uhr, Mo - Mi: 13:30 - 15:30 Uhr,	poststelle@fa-wen.bayern.de	www.finanzamt-weiden.de	682
9	9257	Würzburg mit Außenstelle Ochsenfurt	Ludwigstr. 25	97070	Würzburg	0931 387-0	0931 387-4444	97064			79000000	79001500	BBK WUERZBURG	79020076	801283	HYPOVEREINSBK WUERZBURG	Servicezentrum: Mo - Mi: 07:30 - 15:00 Uhr, Do: 07:30 - 17:00 Uhr, Fr: 07:30	poststelle@fa-wue.bayern.de	www.finanzamt-wuerzburg.de	683
9	9258	Wunsiedel mit Außenstelle Selb	Sonnenstr. 11	95632	Wunsiedel	09232 607-0	09232 607-200	95631			78000000	78101512	BBK BAYREUTH EH HOF	78055050	620006254	SPK FICHTELGEBIRGE	Servicezentrum: Mo-Mi: 07:30-12:30 u 13:30-15:00, Do: 07:30-12:30 und 13:30-17:00, Fr: 07:30-12:00 h	poststelle@fa-wun.bayern.de	www.finanzamt-wunsiedel.de	684
9	9259	Zeil a. Main mit Außenstellen  	Obere Torstr. 9	97475	Zeil	09524 824-0	09524 824-100		97470	1160	79300000	79301505	BBK WUERZBURG EH SCHWEINFUR	79351730	500900	SPK OSTUNTERFRANKEN	Servicezentrum: Mo - Mi: 08:00 - 15:00 Uhr, Do: 08:00 - 17:00 Uhr, Fr: 08:00	poststelle@fa-zei.bayern.de	www.finanzamt-zeil.de	685
9	9260	Kötzting - Außenstelle des Finanzamts Cham -	Bahnhofstr. 3	93444	Kötzting	09971 488-0	09971 488-450				74221170	344 755 205	HYPOVEREINSBK CHAM, OBERPF	76010085	1735-858	POSTBANK NUERNBERG	Servicezentrum: Mo - Mi: 07:30 - 15:00 Uhr, Do: 07:30 - 18:00 Uhr, Fr: 07:30	poststelle@fa-koez.bayern.de	www.finanzamt-koetzting.de	686
2	2241	Hamburg-Altona 	Gr. Bergstr. 264/266	22767	Hamburg	040/42811-02	040/42811-2871		22704	500471	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAHamburgAltona@finanzamt.hamburg.de		58
2	2243	Hamburg-Barmbek-Uhlenhorst 	Lübecker Str. 101-109	22087	Hamburg	040/42860-0	040/42860-730		22053	760360	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FABarmbekUhlenhorst@finanzamt.hamburg.de		78
2	2243	Hamburg-Barmbek-Uhlenhorst 15  	Lübecker Str. 101-109	22087	Hamburg	040/42860-0	040/42860-730		22053	760360	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAHamburgBarmbekUhlenhorst@finanzamt.hamburg.de		64
2	2244	Hamburg-Bergedorf 	Ludwig-Rosenberg-Ring 41	21031	Hamburg	040/42891-0	040/42891-2243		21003	800360	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		FAHamburgBergedorf@finanzamt.hamburg.de		59
2	2245	Hamburg-Eimsbüttel 	Stresemannstraße 23	22769	Hamburg	040/42807-0	040/42807-220		22770	570110	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAHamburgEimsbuettel@finanzamt.hamburg.de		76
2	2246	Hamburg-Hansa 	Steinstraße 10	20095	Hamburg	040/42853-01	040/42853-2064		20015	102244	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		FAHamburgHansa@finanzamt.hamburg.de		68
2	2247	Hamburg-Harburg 	Harburger Ring 40	21073	Hamburg	040/42871-0	040/42871-2215		21043	900352	20000000	200 015 30	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAHamburgHarburg@finanzamt.hamburg.de		60
2	2249	Hamburg-Nord 	Borsteler Chaussee 45	22453	Hamburg	040/42806-0	040/42806-220		22207	600707	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		FAHamburgNord@finanzamt.hamburg.de		71
2	2250	Hamburg-Oberalster 	Hachmannplatz 2	20099	Hamburg	040/42854-90	040/42854-4960		20015	102248	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAHamburgOberalster@finanzamt.hamburg.de		62
2	2251	Hamburg-Wandsbek 	Schloßstr.107	22041	Hamburg	040/42881-0	040/42881-2888		22006	700660	20000000	20001530	BBK HAMBURG	21050000	101444000	HSH NORDBANK KIEL		 FAHamburgWandsbek@finanzamt.hamburg.de		61
6	2603	Bad Homburg v.d. Höhe 	Kaiser-Friedr.-Promenade 8-10 	61348	Bad Homburg	06172/107-0	06172/107-317	61343	61284	1445	50050000	1000124	Landesbank Hessen-Thüringen	50000000	50001501	DT BBK Filiale Frankfurt am Main	Mo u. Fr 8:00-12:00, Mi 14:00-18:00 Uhr	poststelle@Finanzamt-Bad-Homburg.de	www.Finanzamt-Bad-Homburg.de	162
\.


--
-- Name: finanzamt_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('finanzamt_id_seq', 686, true);


--
-- Data for Name: follow_up_access; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY follow_up_access (who, what, id) FROM stdin;
\.


--
-- Name: follow_up_access_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('follow_up_access_id_seq', 1, false);


--
-- Name: follow_up_id; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('follow_up_id', 1, false);


--
-- Name: follow_up_link_id; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('follow_up_link_id', 1, false);


--
-- Data for Name: follow_up_links; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY follow_up_links (id, follow_up_id, trans_id, trans_type, trans_info, itime, mtime) FROM stdin;
\.


--
-- Data for Name: follow_ups; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY follow_ups (id, follow_up_date, created_for_user, done, note_id, created_by, itime, mtime) FROM stdin;
\.


--
-- Data for Name: generic_translations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY generic_translations (id, language_id, translation_type, translation_id, translation) FROM stdin;
\.


--
-- Name: generic_translations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('generic_translations_id_seq', 1, false);


--
-- Data for Name: gl; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY gl (id, reference, description, transdate, gldate, employee_id, notes, department_id, taxincluded, itime, mtime, type, ob_transaction, cb_transaction, storno, storno_id) FROM stdin;
\.


--
-- Name: glid; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('glid', 1, false);


--
-- Data for Name: grpusr; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY grpusr (gid, grpid, usrid) FROM stdin;
\.


--
-- Data for Name: gruppenname; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY gruppenname (grpid, grpname, rechte) FROM stdin;
\.


--
-- Data for Name: history; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY history (mid, itime, art, bezug, beschreibung) FROM stdin;
\.


--
-- Data for Name: history_erp; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY history_erp (id, trans_id, employee_id, addition, what_done, itime, snumbers) FROM stdin;
\.


--
-- Name: id; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('id', 893, true);


--
-- Data for Name: inventory; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY inventory (warehouse_id, parts_id, oe_id, delivery_order_items_stock_id, shippingdate, employee_id, itime, mtime, bin_id, qty, trans_id, trans_type_id, project_id, chargenumber, comment, bestbefore, id) FROM stdin;
\.


--
-- Name: inventory_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('inventory_id_seq', 1, false);


--
-- Data for Name: invoice; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY invoice (id, trans_id, parts_id, description, qty, allocated, sellprice, fxsellprice, discount, assemblyitem, project_id, deliverydate, serialnumber, itime, mtime, pricegroup_id, ordnumber, transdate, cusordnumber, unit, base_qty, subtotal, longdescription, marge_total, marge_percent, lastcost, price_factor_id, price_factor, marge_price_factor, donumber, "position", active_price_source, active_discount_source) FROM stdin;
\.


--
-- Name: invoiceid; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('invoiceid', 1, false);


--
-- Data for Name: labels; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY labels (id, name, cust, papersize, metric, marginleft, margintop, nx, ny, spacex, spacey, width, height, fontsize, employee) FROM stdin;
1	Firma	C	A4	mm	2	2	2	3	4	2	66	38	10	\N
\.


--
-- Data for Name: labeltxt; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY labeltxt (id, lid, font, zeile) FROM stdin;
2	1	6	
3	1	8	Lx-System, Unser Weg 1, 12345 Woanders
4	1	6	
5	1	10	%ANREDE%
6	1	10	%NAME1% %NAME2%
7	1	10	!%KONTAKT%|%DEPARTMENT%
8	1	10	%STRASSE%
9	1	8	
10	1	10	%PLZ% %ORT%
\.


--
-- Data for Name: language; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY language (id, description, template_code, article_code, itime, mtime, output_numberformat, output_dateformat, output_longdates) FROM stdin;
\.


--
-- Data for Name: leads; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY leads (id, lead) FROM stdin;
\.


--
-- Data for Name: mailvorlage; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY mailvorlage (id, cause, c_long, employee) FROM stdin;
\.


--
-- Data for Name: makemodel; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY makemodel (parts_id, model, itime, mtime, lastcost, lastupdate, sortorder, make, id) FROM stdin;
\.


--
-- Name: makemodel_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('makemodel_id_seq', 1, false);


--
-- Data for Name: maschine; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY maschine (id, parts_id, serialnumber, standort, inspdatum, counter) FROM stdin;
\.


--
-- Data for Name: maschmat; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY maschmat (mid, aid, parts_id, betrag, menge) FROM stdin;
\.


--
-- Name: note_id; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('note_id', 1, false);


--
-- Data for Name: notes; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY notes (id, subject, body, created_by, trans_id, trans_module, itime, mtime) FROM stdin;
\.


--
-- Data for Name: oe; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY oe (id, ordnumber, transdate, vendor_id, customer_id, amount, netamount, reqdate, taxincluded, shippingpoint, notes, employee_id, closed, quotation, quonumber, cusordnumber, intnotes, department_id, itime, mtime, shipvia, cp_id, language_id, payment_id, delivery_customer_id, delivery_vendor_id, taxzone_id, proforma, shipto_id, order_probability, expected_billing_date, globalproject_id, delivered, salesman_id, marge_total, marge_percent, transaction_description, delivery_term_id, currency_id) FROM stdin;
\.


--
-- Data for Name: opport_status; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY opport_status (id, statusname, sort) FROM stdin;
11	Neu	1
12	Wert-Angebot	2
13	Entscheidungsfindung	3
14	bedarf Analyse	4
15	Gewonnen	5
16	Aufgeschoben	6
17	wieder offen	7
18	Verloren	8
\.


--
-- Data for Name: opportunity; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY opportunity (id, oppid, fid, tab, title, betrag, zieldatum, chance, status, salesman, next, notiz, auftrag, itime, iemployee, memployee) FROM stdin;
\.


--
-- Data for Name: orderitems; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY orderitems (trans_id, parts_id, description, qty, sellprice, discount, project_id, reqdate, ship, serialnumber, id, itime, mtime, pricegroup_id, ordnumber, transdate, cusordnumber, unit, base_qty, subtotal, longdescription, marge_total, marge_percent, lastcost, price_factor_id, price_factor, marge_price_factor, "position", active_price_source, active_discount_source) FROM stdin;
\.


--
-- Name: orderitemsid; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('orderitemsid', 1, false);


--
-- Data for Name: parts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY parts (id, partnumber, description, listprice, sellprice, lastcost, priceupdate, weight, notes, makemodel, assembly, alternate, rop, inventory_accno_id, income_accno_id, expense_accno_id, shop, obsolete, bom, image, drawing, microfiche, partsgroup_id, ve, gv, itime, mtime, unit, formel, not_discountable, buchungsgruppen_id, payment_id, ean, price_factor_id, onhand, stockable, has_sernumber, warehouse_id, bin_id) FROM stdin;
\.


--
-- Data for Name: partsgroup; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY partsgroup (id, partsgroup, itime, mtime) FROM stdin;
\.


--
-- Data for Name: payment_terms; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY payment_terms (id, description, description_long, terms_netto, terms_skonto, percent_skonto, itime, mtime, ranking, sortkey) FROM stdin;
\.


--
-- Data for Name: periodic_invoices; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY periodic_invoices (id, config_id, ar_id, period_start_date, itime) FROM stdin;
\.


--
-- Data for Name: periodic_invoices_configs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY periodic_invoices_configs (id, oe_id, periodicity, print, printer_id, copies, active, terminated, start_date, end_date, ar_chart_id, extend_automatically_by, first_billing_date) FROM stdin;
\.


--
-- Data for Name: postit; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY postit (id, cause, notes, employee, date) FROM stdin;
\.


--
-- Data for Name: price_factors; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY price_factors (id, description, factor, sortkey) FROM stdin;
876	pro 10	10.00000	1
877	pro 100	100.00000	2
878	pro 1.000	1000.00000	3
\.


--
-- Data for Name: price_rule_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY price_rule_items (id, price_rules_id, type, op, custom_variable_configs_id, value_text, value_int, value_date, value_num, itime, mtime) FROM stdin;
\.


--
-- Name: price_rule_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('price_rule_items_id_seq', 1, false);


--
-- Data for Name: price_rules; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY price_rules (id, name, type, priority, price, reduction, obsolete, itime, mtime, discount) FROM stdin;
\.


--
-- Name: price_rules_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('price_rules_id_seq', 1, false);


--
-- Data for Name: pricegroup; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY pricegroup (id, pricegroup) FROM stdin;
\.


--
-- Data for Name: prices; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY prices (parts_id, pricegroup_id, price, id) FROM stdin;
\.


--
-- Name: prices_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('prices_id_seq', 1, false);


--
-- Data for Name: printers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY printers (id, printer_description, printer_command, template_code) FROM stdin;
\.


--
-- Data for Name: project; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY project (id, projectnumber, description, itime, mtime, active, customer_id, valid, project_type_id, start_date, end_date, billable_customer_id, budget_cost, order_value, budget_minutes, timeframe, project_status_id) FROM stdin;
\.


--
-- Data for Name: project_participants; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY project_participants (id, project_id, employee_id, project_role_id, minutes, cost_per_hour, itime, mtime) FROM stdin;
\.


--
-- Name: project_participants_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('project_participants_id_seq', 1, false);


--
-- Data for Name: project_phase_participants; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY project_phase_participants (id, project_phase_id, employee_id, project_role_id, minutes, cost_per_hour, itime, mtime) FROM stdin;
\.


--
-- Name: project_phase_participants_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('project_phase_participants_id_seq', 1, false);


--
-- Data for Name: project_phases; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY project_phases (id, project_id, start_date, end_date, name, description, budget_minutes, budget_cost, general_minutes, general_cost_per_hour, itime, mtime) FROM stdin;
\.


--
-- Name: project_phases_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('project_phases_id_seq', 1, false);


--
-- Data for Name: project_roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY project_roles (id, name, description, "position", itime, mtime) FROM stdin;
\.


--
-- Name: project_roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('project_roles_id_seq', 1, false);


--
-- Name: project_status_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('project_status_id_seq', 4, true);


--
-- Data for Name: project_statuses; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY project_statuses (id, name, description, "position", itime, mtime) FROM stdin;
1	presales	Akquise	1	2015-02-01 15:52:41.274578	\N
2	planning	In Planung	2	2015-02-01 15:52:41.274578	\N
3	running	In Bearbeitung	3	2015-02-01 15:52:41.274578	\N
4	done	Fertiggestellt	4	2015-02-01 15:52:41.274578	\N
\.


--
-- Data for Name: project_types; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY project_types (id, "position", description, internal) FROM stdin;
1	1	Standard	f
2	2	Festpreis	f
3	3	Support	f
\.


--
-- Name: project_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('project_types_id_seq', 3, true);


--
-- Data for Name: record_links; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY record_links (from_table, from_id, to_table, to_id, itime, id) FROM stdin;
\.


--
-- Name: record_links_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('record_links_id_seq', 1, false);


--
-- Data for Name: repauftrag; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY repauftrag (aid, mid, cause, schaden, reparatur, bearbdate, employee, bearbeiter, anlagedatum, status, kdnr, counter) FROM stdin;
\.


--
-- Data for Name: requirement_spec_acceptance_statuses; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_acceptance_statuses (id, name, description, "position", itime, mtime) FROM stdin;
1	accepted	Abgenommen	1	2015-02-01 15:52:40.296807	\N
2	accepted_with_defects	Mit Mängeln abgenommen	2	2015-02-01 15:52:40.296807	\N
3	accepted_with_defects_to_be_fixed	Mit noch zu behebenden Mängeln abgenommen	3	2015-02-01 15:52:40.296807	\N
4	not_accepted	Nicht abgenommen	4	2015-02-01 15:52:40.296807	\N
\.


--
-- Name: requirement_spec_acceptance_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_acceptance_statuses_id_seq', 4, true);


--
-- Data for Name: requirement_spec_complexities; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_complexities (id, description, "position", itime, mtime) FROM stdin;
1	nicht bewertet	1	2015-02-01 15:52:40.296807	\N
2	nur Anforderung	2	2015-02-01 15:52:40.296807	\N
3	gering	3	2015-02-01 15:52:40.296807	\N
4	mittel	4	2015-02-01 15:52:40.296807	\N
5	hoch	5	2015-02-01 15:52:40.296807	\N
\.


--
-- Name: requirement_spec_complexities_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_complexities_id_seq', 5, true);


--
-- Data for Name: requirement_spec_item_dependencies; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_item_dependencies (depending_item_id, depended_item_id) FROM stdin;
\.


--
-- Data for Name: requirement_spec_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_items (id, requirement_spec_id, item_type, parent_id, "position", fb_number, title, description, complexity_id, risk_id, time_estimation, is_flagged, acceptance_status_id, acceptance_text, itime, mtime, order_part_id) FROM stdin;
\.


--
-- Name: requirement_spec_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_items_id_seq', 1, false);


--
-- Data for Name: requirement_spec_orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_orders (id, requirement_spec_id, order_id, version_id, itime, mtime) FROM stdin;
\.


--
-- Name: requirement_spec_orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_orders_id_seq', 1, false);


--
-- Data for Name: requirement_spec_parts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_parts (id, requirement_spec_id, part_id, unit_id, qty, description, "position") FROM stdin;
\.


--
-- Name: requirement_spec_parts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_parts_id_seq', 1, false);


--
-- Data for Name: requirement_spec_pictures; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_pictures (id, requirement_spec_id, text_block_id, "position", number, description, picture_file_name, picture_content_type, picture_mtime, picture_content, picture_width, picture_height, thumbnail_content_type, thumbnail_content, thumbnail_width, thumbnail_height, itime, mtime) FROM stdin;
\.


--
-- Name: requirement_spec_pictures_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_pictures_id_seq', 1, false);


--
-- Data for Name: requirement_spec_predefined_texts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_predefined_texts (id, description, title, text, "position", itime, mtime, useable_for_text_blocks, useable_for_sections) FROM stdin;
\.


--
-- Name: requirement_spec_predefined_texts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_predefined_texts_id_seq', 1, false);


--
-- Data for Name: requirement_spec_risks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_risks (id, description, "position", itime, mtime) FROM stdin;
1	nicht bewertet	1	2015-02-01 15:52:40.296807	\N
2	nur Anforderung	2	2015-02-01 15:52:40.296807	\N
3	gering	3	2015-02-01 15:52:40.296807	\N
4	mittel	4	2015-02-01 15:52:40.296807	\N
5	hoch	5	2015-02-01 15:52:40.296807	\N
\.


--
-- Name: requirement_spec_risks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_risks_id_seq', 5, true);


--
-- Data for Name: requirement_spec_statuses; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_statuses (id, name, description, "position", itime, mtime) FROM stdin;
1	planning	In Planung	1	2015-02-01 15:52:40.296807	\N
2	running	In Bearbeitung	2	2015-02-01 15:52:40.296807	\N
3	done	Fertiggestellt	3	2015-02-01 15:52:40.296807	\N
\.


--
-- Name: requirement_spec_statuses_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_statuses_id_seq', 3, true);


--
-- Data for Name: requirement_spec_text_blocks; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_text_blocks (id, requirement_spec_id, title, text, "position", output_position, is_flagged, itime, mtime) FROM stdin;
\.


--
-- Name: requirement_spec_text_blocks_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_text_blocks_id_seq', 1, false);


--
-- Data for Name: requirement_spec_types; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_types (id, description, "position", itime, mtime, section_number_format, function_block_number_format, template_file_name) FROM stdin;
1	Pflichtenheft	1	2015-02-01 15:52:40.296807	2015-02-01 15:52:40.884291	A00	FB000	requirement_spec
2	Konzept	2	2015-02-01 15:52:40.296807	2015-02-01 15:52:40.884291	A00	FB000	requirement_spec
\.


--
-- Name: requirement_spec_types_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_types_id_seq', 2, true);


--
-- Data for Name: requirement_spec_versions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_spec_versions (id, version_number, description, comment, itime, mtime, requirement_spec_id, working_copy_id) FROM stdin;
\.


--
-- Name: requirement_spec_versions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_spec_versions_id_seq', 1, false);


--
-- Data for Name: requirement_specs; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY requirement_specs (id, type_id, status_id, customer_id, project_id, title, hourly_rate, working_copy_id, previous_section_number, previous_fb_number, is_template, itime, mtime, time_estimation, previous_picture_number) FROM stdin;
\.


--
-- Name: requirement_specs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('requirement_specs_id_seq', 1, false);


--
-- Data for Name: schema_info; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY schema_info (tag, login, itime) FROM stdin;
SKR04-3804-addition	demo	2015-02-01 15:52:37.611022
acc_trans_constraints	demo	2015-02-01 15:52:37.616041
chart_category_to_sgn	demo	2015-02-01 15:52:37.621779
chart_names	demo	2015-02-01 15:52:37.62587
chart_names2	demo	2015-02-01 15:52:37.63356
customer_vendor_ustid_length	demo	2015-02-01 15:52:37.63869
language_output_formatting	demo	2015-02-01 15:52:37.648197
rename_buchungsgruppen_accounts_16_19_percent	demo	2015-02-01 15:52:37.652867
sales_quotation_order_probability_expected_billing_date	demo	2015-02-01 15:52:37.662803
tax_id_if_taxkey_is_0	demo	2015-02-01 15:52:37.669071
units_translations_and_singular_plural_distinction	demo	2015-02-01 15:52:37.677177
tax_primary_key_taxkeys_foreign_keys	demo	2015-02-01 15:52:37.68654
invalid_taxkesy	demo	2015-02-01 15:52:37.696516
release_2_4_1	demo	2015-02-01 15:52:37.702208
PgCommaAggregateFunction	demo	2015-02-01 15:52:37.705101
ap_ar_orddate_quodate	demo	2015-02-01 15:52:37.709198
buchungsgruppen_sortkey	demo	2015-02-01 15:52:37.714097
customer_vendor_taxzone_id	demo	2015-02-01 15:52:37.723105
drafts	demo	2015-02-01 15:52:37.730809
employee_no_limits	demo	2015-02-01 15:52:37.739577
globalprojectnumber_ap_ar_oe	demo	2015-02-01 15:52:37.758813
oe_delivered	demo	2015-02-01 15:52:37.768416
oe_is_salesman	demo	2015-02-01 15:52:37.773368
parts_ean	demo	2015-02-01 15:52:37.780727
payment_terms_sortkey	demo	2015-02-01 15:52:37.784041
payment_terms_translation	demo	2015-02-01 15:52:37.790908
project	demo	2015-02-01 15:52:37.797757
status_history	demo	2015-02-01 15:52:37.802358
units_sortkey	demo	2015-02-01 15:52:37.811315
history_erp	demo	2015-02-01 15:52:37.817718
marge_initial	demo	2015-02-01 15:52:37.829188
ustva_setup_2007	demo	2015-02-01 15:52:37.837609
history_erp_snumbers	demo	2015-02-01 15:52:37.846597
tax_description_without_percentage_skr04	demo	2015-02-01 15:52:37.851155
ustva_setup_2007_update_chart_taxkeys_tax	demo	2015-02-01 15:52:37.870408
fix_taxdescription	demo	2015-02-01 15:52:37.958739
ustva_setup_2007_update_chart_taxkeys_tax_add_missing_tax_accounts	demo	2015-02-01 15:52:37.985096
tax_description_without_percentage	demo	2015-02-01 15:52:37.989636
release_2_4_2	demo	2015-02-01 15:52:38.008813
COA_Account_Settings001	demo	2015-02-01 15:52:38.011708
COA_Account_Settings002	demo	2015-02-01 15:52:38.015867
USTVA_abstraction	demo	2015-02-01 15:52:38.020931
ap_storno	demo	2015-02-01 15:52:38.063534
ar_storno	demo	2015-02-01 15:52:38.068415
cb_ob_transaction	demo	2015-02-01 15:52:38.07311
dunning_config_interest_rate	demo	2015-02-01 15:52:38.077486
dunning_dunning_id	demo	2015-02-01 15:52:38.080231
dunning_invoices_for_fees	demo	2015-02-01 15:52:38.092272
gl_storno	demo	2015-02-01 15:52:38.100086
invalid_taxkeys_2	demo	2015-02-01 15:52:38.112689
transaction_description	demo	2015-02-01 15:52:38.117552
USTVA_at	demo	2015-02-01 15:52:38.124109
ar_ap_storno_id	demo	2015-02-01 15:52:38.127783
dunning_invoices_per_dunning_level	demo	2015-02-01 15:52:38.133278
tax_report_table_name	demo	2015-02-01 15:52:38.138132
release_2_4_3	demo	2015-02-01 15:52:38.14067
acc_trans_without_oid	demo	2015-02-01 15:52:38.142951
bank_accounts	demo	2015-02-01 15:52:38.171998
change_makemodel_vendor_id	demo	2015-02-01 15:52:38.185998
custom_variables	demo	2015-02-01 15:52:38.189876
direct_debit	demo	2015-02-01 15:52:38.209457
follow_ups	demo	2015-02-01 15:52:38.214282
oe_employee_id_foreignkey	demo	2015-02-01 15:52:38.244222
price_factors	demo	2015-02-01 15:52:38.249173
sic_code	demo	2015-02-01 15:52:38.271713
todo_config	demo	2015-02-01 15:52:38.27818
trigger_assembly_update_lastcost	demo	2015-02-01 15:52:38.283424
units_no_type_distinction	demo	2015-02-01 15:52:38.292335
warehouse	demo	2015-02-01 15:52:38.295851
delivery_orders	demo	2015-02-01 15:52:38.331419
transfer_type_shipped	demo	2015-02-01 15:52:38.371609
warehouse2	demo	2015-02-01 15:52:38.375557
ar_add_donumber	demo	2015-02-01 15:52:38.380224
ar_add_invnumber_for_credit_note	demo	2015-02-01 15:52:38.383148
check_bin_belongs_to_wh_trigger	demo	2015-02-01 15:52:38.387105
record_links	demo	2015-02-01 15:52:38.392665
transaction_description_not_null	demo	2015-02-01 15:52:38.404225
release_2_6_0	demo	2015-02-01 15:52:38.412035
auth_enable_sales_all_edit	demo	2015-02-01 15:52:38.416356
custom_variables_parts_services_assemblies	demo	2015-02-01 15:52:38.418888
custom_variables_valid	demo	2015-02-01 15:52:38.424145
delivery_orders_fields_for_invoices	demo	2015-02-01 15:52:38.430237
fix_acc_trans_ap_taxkey_bug	demo	2015-02-01 15:52:38.436398
fix_datepaid	demo	2015-02-01 15:52:38.444153
generic_translations	demo	2015-02-01 15:52:38.451192
has_sernumber	demo	2015-02-01 15:52:38.462104
rundungsfehler_korrigieren_BUG1328-2	demo	2015-02-01 15:52:38.466256
sepa	demo	2015-02-01 15:52:38.473147
update_date_paid	demo	2015-02-01 15:52:38.488303
warehouse3	demo	2015-02-01 15:52:38.494597
warehouse_add_bestbefore	demo	2015-02-01 15:52:38.498451
add_depositor_for_customer_vendor	demo	2015-02-01 15:52:38.501987
add_more_constraints_fibu_projekt_xplace3	demo	2015-02-01 15:52:38.512223
cp_greeting_migration	demo	2015-02-01 15:52:38.520607
release_2_6_1	demo	2015-02-01 15:52:38.525283
acc_trans_id_uniqueness	demo	2015-02-01 15:52:38.52813
add_ar_paid_defaults	demo	2015-02-01 15:52:38.532257
add_makemodel_prices	demo	2015-02-01 15:52:38.535126
csv_import_profiles	demo	2015-02-01 15:52:38.541567
customer_long_entries	demo	2015-02-01 15:52:38.559327
drop_yearend	demo	2015-02-01 15:52:38.565864
emmvee_background_jobs	demo	2015-02-01 15:52:38.569693
invalid_entries_in_custom_variables_validity	demo	2015-02-01 15:52:38.583527
payment_terms_translation2	demo	2015-02-01 15:52:38.588044
periodic_invoices	demo	2015-02-01 15:52:38.595321
schema_normalization_1	demo	2015-02-01 15:52:38.608879
sepa_in	demo	2015-02-01 15:52:38.632138
shipto_add_cp_gender	demo	2015-02-01 15:52:38.64059
skr03_04_bwa_zuordnung_konten_4250_4610	demo	2015-02-01 15:52:38.643744
skr04_fix_category_3151_3160_3170	demo	2015-02-01 15:52:38.652247
ustva_2010	demo	2015-02-01 15:52:38.656365
csv_import_profiles_2	demo	2015-02-01 15:52:38.664099
delete_translations_on_payment_term_delete	demo	2015-02-01 15:52:38.667711
emmvee_background_jobs_2	demo	2015-02-01 15:52:38.767283
periodic_invoices_background_job	demo	2015-02-01 15:52:38.962339
periodic_invoices_first_billing_date	demo	2015-02-01 15:52:38.964807
schema_normalization_2	demo	2015-02-01 15:52:38.9676
background_jobs_3	demo	2015-02-01 15:52:39.147311
csv_import_report_cache	demo	2015-02-01 15:52:39.15041
schema_normalization_3	demo	2015-02-01 15:52:39.174951
csv_import_reports_add_numheaders	demo	2015-02-01 15:52:39.181383
release_2_6_2	demo	2015-02-01 15:52:39.185832
chart_taxkey_id_from_taxkeys	demo	2015-02-01 15:52:39.18863
custom_variables_indices	demo	2015-02-01 15:52:39.240365
custom_variables_indices_2	demo	2015-02-01 15:52:39.246506
units_id	demo	2015-02-01 15:52:39.250009
release_2_6_3	demo	2015-02-01 15:52:39.260784
auth_enable_ct_all_edit	demo	2015-02-01 15:52:39.265103
auth_enable_edit_prices	demo	2015-02-01 15:52:39.269106
customer_add_constraints	demo	2015-02-01 15:52:39.271777
customer_vendor_add_currency	demo	2015-02-01 15:52:39.283277
defaults_add_language_id	demo	2015-02-01 15:52:39.2869
delivery_order_items_add_pricegroup_id	demo	2015-02-01 15:52:39.289913
department_drop_role	demo	2015-02-01 15:52:39.293057
drop_datevexport	demo	2015-02-01 15:52:39.29608
employee_deleted	demo	2015-02-01 15:52:39.301185
license_invoice_drop	demo	2015-02-01 15:52:39.315526
oe_customer_vendor_fkeys	demo	2015-02-01 15:52:39.321837
parts_add_unit_foreign_key	demo	2015-02-01 15:52:39.330904
umstellung_eur	demo	2015-02-01 15:52:39.338436
ustva_2010_fixes	demo	2015-02-01 15:52:39.34185
vendor_add_constraints	demo	2015-02-01 15:52:39.345707
warehouse_alter_chargenumber	demo	2015-02-01 15:52:39.357414
release_2_7_0	demo	2015-02-01 15:52:39.362011
chart_type_skonto	demo	2015-02-01 15:52:39.364595
contacts_add_street_and_zipcode_and_city	demo	2015-02-01 15:52:39.379494
contacts_convert_cp_birthday_to_date	demo	2015-02-01 15:52:39.383796
convert_curr_to_text	demo	2015-02-01 15:52:39.38793
custom_variables_sub_module_not_null	demo	2015-02-01 15:52:39.492969
customer_add_taxincluded_checked	demo	2015-02-01 15:52:39.498017
customer_vendor_phone_no_limits	demo	2015-02-01 15:52:39.514974
defaults_datev_check	demo	2015-02-01 15:52:39.519117
defaults_posting_config	demo	2015-02-01 15:52:39.564418
defaults_posting_records_config	demo	2015-02-01 15:52:39.576745
defaults_show_bestbefore	demo	2015-02-01 15:52:39.623237
defaults_show_delete_on_orders	demo	2015-02-01 15:52:39.636335
defaults_show_mark_as_paid_config	demo	2015-02-01 15:52:39.67505
finanzamt_update_fa_bufa_nr_hamburg	demo	2015-02-01 15:52:39.713559
record_links_post_delete_triggers	demo	2015-02-01 15:52:39.724219
rename_buchungsgruppe_16_19_to_19	demo	2015-02-01 15:52:39.745284
self_test_background_job	demo	2015-02-01 15:52:39.799208
ustva_setup_2007_update_chart_taxkeys_tax_skr04	demo	2015-02-01 15:52:39.802947
customer_add_taxincluded_checked_2	demo	2015-02-01 15:52:39.816095
record_links_post_delete_triggers2	demo	2015-02-01 15:52:39.820971
release_3_0_0	demo	2015-02-01 15:52:39.827398
acc_trans_booleans_not_null	demo	2015-02-01 15:52:39.829791
accounts_tax_office_bad_homburg	demo	2015-02-01 15:52:39.83989
add_chart_link_to_acc_trans	demo	2015-02-01 15:52:39.843878
add_customer_mandator_id	demo	2015-02-01 15:52:39.850853
add_fk_to_gl	demo	2015-02-01 15:52:39.858819
add_warehouse_defaults	demo	2015-02-01 15:52:39.865468
ap_add_direct_debit	demo	2015-02-01 15:52:39.873938
ap_deliverydate	demo	2015-02-01 15:52:39.878534
ar_add_direct_debit	demo	2015-02-01 15:52:39.881246
ar_ap_foreign_keys	demo	2015-02-01 15:52:39.885554
ar_ap_gl_delete_triggers_deletion_from_acc_trans	demo	2015-02-01 15:52:39.922906
background_job_change_create_periodic_invoices_to_daily	demo	2015-02-01 15:52:39.944874
charts_without_taxkey	demo	2015-02-01 15:52:39.947643
cleanup_after_customer_vendor_deletion	demo	2015-02-01 15:52:39.953768
clients	demo	2015-02-01 15:52:39.958237
contacts_add_cp_position	demo	2015-02-01 15:52:39.963086
custom_variable_configs_column_type_text	demo	2015-02-01 15:52:39.966232
custom_variables_validity_index	demo	2015-02-01 15:52:39.974731
defaults_add_max_future_booking_intervall	demo	2015-02-01 15:52:39.980172
defaults_feature	demo	2015-02-01 15:52:39.991355
defaults_feature2	demo	2015-02-01 15:52:40.053872
del_exchangerate	demo	2015-02-01 15:52:40.076295
delete_close_follow_ups_when_order_is_deleted_closed_fkey_deletion	demo	2015-02-01 15:52:40.082064
delete_customertax_vendortax_partstax	demo	2015-02-01 15:52:40.091144
delete_translations_on_tax_delete	demo	2015-02-01 15:52:40.098977
delivery_terms	demo	2015-02-01 15:52:40.105896
drop_audittrail	demo	2015-02-01 15:52:40.12938
drop_dpt_trans	demo	2015-02-01 15:52:40.134675
drop_gifi	demo	2015-02-01 15:52:40.144375
drop_rma	demo	2015-02-01 15:52:40.150519
employee_drop_columns	demo	2015-02-01 15:52:40.157641
erzeugnisnummern	demo	2015-02-01 15:52:40.167526
fix_datepaid_for_sepa_transfers	demo	2015-02-01 15:52:40.175254
gewichte	demo	2015-02-01 15:52:40.180001
gl_add_employee_foreign_key	demo	2015-02-01 15:52:40.192286
invoice_add_donumber	demo	2015-02-01 15:52:40.197004
oe_delivery_orders_foreign_keys	demo	2015-02-01 15:52:40.200032
orderitems_delivery_order_items_invoice_foreign_keys	demo	2015-02-01 15:52:40.231559
parts_translation_foreign_keys	demo	2015-02-01 15:52:40.256041
project_customer_type_valid	demo	2015-02-01 15:52:40.269624
project_types	demo	2015-02-01 15:52:40.277828
requirement_specs	demo	2015-02-01 15:52:40.296807
rm_whitespaces	demo	2015-02-01 15:52:40.538175
add_tax_id_to_acc_trans	demo	2015-02-01 15:52:40.548118
add_warehouse_client_config_default	demo	2015-02-01 15:52:40.556947
balance_startdate_method	demo	2015-02-01 15:52:40.593303
currencies	demo	2015-02-01 15:52:40.59999
custom_variables_delete_via_trigger	demo	2015-02-01 15:52:40.632753
default_bin_parts	demo	2015-02-01 15:52:40.636898
defaults_customer_hourly_rate	demo	2015-02-01 15:52:40.644587
defaults_signature	demo	2015-02-01 15:52:40.649587
delete_close_follow_ups_when_order_is_deleted_closed	demo	2015-02-01 15:52:40.652916
delete_cust_vend_tax	demo	2015-02-01 15:52:40.672192
delete_translations_on_delivery_term_delete	demo	2015-02-01 15:52:40.676904
drop_gifi_2	demo	2015-02-01 15:52:40.682478
oe_do_delete_via_trigger	demo	2015-02-01 15:52:40.685626
project_bob_attributes	demo	2015-02-01 15:52:40.738705
remove_role_from_employee	demo	2015-02-01 15:52:40.827963
requirement_spec_items_item_type_index	demo	2015-02-01 15:52:40.831455
requirement_spec_items_update_trigger_fix	demo	2015-02-01 15:52:40.836278
requirement_spec_pictures	demo	2015-02-01 15:52:40.850733
requirement_spec_predefined_texts_for_sections	demo	2015-02-01 15:52:40.86477
requirement_spec_types_number_formats	demo	2015-02-01 15:52:40.87242
requirement_spec_types_template_file_name	demo	2015-02-01 15:52:40.884291
requirement_specs_print_templates	demo	2015-02-01 15:52:40.888751
requirement_specs_section_templates	demo	2015-02-01 15:52:40.89298
tax_constraints	demo	2015-02-01 15:52:40.89935
add_fkey_tax_id_to_acc_trans	demo	2015-02-01 15:52:40.944318
custom_variables_delete_via_trigger_2	demo	2015-02-01 15:52:40.948095
project_bob_attributes_itime_default_fix	demo	2015-02-01 15:52:40.950666
requirement_spec_delete_trigger_fix	demo	2015-02-01 15:52:40.961415
requirement_spec_type_for_template_fix	demo	2015-02-01 15:52:40.977102
requirement_specs_orders	demo	2015-02-01 15:52:40.981659
steuerfilterung	demo	2015-02-01 15:52:41.003271
unit_foreign_key_for_line_items	demo	2015-02-01 15:52:41.016814
project_bob_attributes_fix_project_status_table_name	demo	2015-02-01 15:52:41.024309
release_3_1_0	demo	2015-02-01 15:52:41.027353
requirement_spec_delete_trigger_fix2	demo	2015-02-01 15:52:41.030014
requirement_spec_items_update_trigger_fix2	demo	2015-02-01 15:52:41.059672
add_warehouse_client_config_default2	demo	2015-02-01 15:52:41.074093
background_jobs_clean_auth_sessions	demo	2015-02-01 15:52:41.114524
bank_accounts_add_name	demo	2015-02-01 15:52:41.116859
column_type_text_instead_of_varchar	demo	2015-02-01 15:52:41.119512
custom_variable_partsgroups	demo	2015-02-01 15:52:41.128604
defaults_add_delivery_plan_config	demo	2015-02-01 15:52:41.136067
defaults_global_bcc	demo	2015-02-01 15:52:41.158587
defaults_only_customer_projects_in_sales	demo	2015-02-01 15:52:41.172313
defaults_reqdate_interval	demo	2015-02-01 15:52:41.17837
defaults_require_transaction_description	demo	2015-02-01 15:52:41.191568
defaults_sales_purchase_process_limitations	demo	2015-02-01 15:52:41.197773
defaults_transport_cost_reminder	demo	2015-02-01 15:52:41.20684
delete_cvars_on_trans_deletion	demo	2015-02-01 15:52:41.213045
invoice_positions	demo	2015-02-01 15:52:41.240422
orderitems_delivery_order_items_positions	demo	2015-02-01 15:52:41.245481
price_rules	demo	2015-02-01 15:52:41.25284
price_source_client_config	demo	2015-02-01 15:52:41.271244
project_status_default_entries	demo	2015-02-01 15:52:41.274578
record_links_orderitems_delete_triggers	demo	2015-02-01 15:52:41.284338
recorditem_active_price_source	demo	2015-02-01 15:52:41.292608
remove_redundant_customer_vendor_delete_triggers	demo	2015-02-01 15:52:41.327516
requirement_spec_edit_html	demo	2015-02-01 15:52:41.333003
requirement_spec_parts	demo	2015-02-01 15:52:41.346361
taxzone_charts	demo	2015-02-01 15:52:41.357776
vendor_long_entries	demo	2015-02-01 15:52:41.36678
warehouse_add_delivery_order_items_stock_id	demo	2015-02-01 15:52:41.373497
column_type_text_instead_of_varchar2	demo	2015-02-01 15:52:41.381156
convert_taxzone	demo	2015-02-01 15:52:41.385535
defaults_transport_cost_reminder_id	demo	2015-02-01 15:52:41.395662
delete_cvars_on_trans_deletion_fix1	demo	2015-02-01 15:52:41.401206
price_rules_cascade_delete	demo	2015-02-01 15:52:41.40426
recorditem_active_record_source	demo	2015-02-01 15:52:41.409359
remove_redundant_cvar_delete_triggers	demo	2015-02-01 15:52:41.43997
taxzone_sortkey	demo	2015-02-01 15:52:41.451082
column_type_text_instead_of_varchar3	demo	2015-02-01 15:52:41.456182
delete_cvars_on_trans_deletion_fix2	demo	2015-02-01 15:52:41.459553
price_rules_discount	demo	2015-02-01 15:52:41.462304
taxzone_default_id	demo	2015-02-01 15:52:41.4655
change_taxzone_id_0	demo	2015-02-01 15:52:41.468625
tax_zones_obsolete	demo	2015-02-01 15:52:41.481611
taxzone_id_in_oe_delivery_orders	demo	2015-02-01 15:52:41.490868
crm_defaults	install	2015-02-01 16:02:41.403868
crm_defaults_gruppe	install	2015-02-01 16:02:41.404951
crm_bundeslaender	install	2015-02-01 16:02:41.405867
crm_CleanContact	install	2015-02-01 16:02:41.406769
crm_employeeFeldLaenge	install	2015-02-01 16:02:41.407651
crm_PrivatTermin	install	2015-02-01 16:02:41.408653
crm_sonderflag	install	2015-02-01 16:02:41.409606
crm_sonderflag2	install	2015-02-01 16:02:41.410598
crm_bundeslaenderutf	install	2015-02-01 16:02:41.411542
crm_CallDirekt	install	2015-02-01 16:02:41.412507
crm_employeeIcal	install	2015-02-01 16:02:41.413493
crm_extrafelder	install	2015-02-01 16:02:41.414447
crm_headcount	install	2015-02-01 16:02:41.415348
crm_lockfile	install	2015-02-01 16:02:41.416258
crm_OpportunityQuotation	install	2015-02-01 16:02:41.417196
crm_Stichwort	install	2015-02-01 16:02:41.418154
crm_streetview	install	2015-02-01 16:02:41.419108
crm_TerminSequenz	install	2015-02-01 16:02:41.420034
crm_TerminDate	install	2015-02-01 16:02:41.421004
crm_TelCallTermin	install	2015-02-01 16:02:41.421936
crm_termincat	install	2015-02-01 16:02:41.422905
crm_TerminCatCol	install	2015-02-01 16:02:41.42386
crm_TerminLocation	install	2015-02-01 16:02:41.424876
crm_timetracker	install	2015-02-01 16:02:41.425933
crm_timetracker_budget	install	2015-02-01 16:02:41.426925
crm_timetracker_parts	install	2015-02-01 16:02:41.427886
crm_wissen_own	install	2015-02-01 16:02:41.428849
crm_wvhistory	install	2015-02-01 16:02:41.429793
crm_CRMemployee	install	2015-02-01 16:02:41.430782
crm_CRMemployeeMID	install	2015-02-01 16:02:41.431746
crm_UserFolder	install	2015-02-01 16:02:41.432771
crm_UserMailssl	install	2015-02-01 16:02:41.433724
crm_WiedervorlageGrp	install	2015-02-01 16:02:41.434671
crm_Calendar	demo	2015-02-01 16:02:41.439045
crm_Calendar02	demo	2015-02-01 16:02:41.439045
crm_EventCategory	demo	2015-02-01 16:02:41.439045
crm_id2login	demo	2015-02-01 16:02:41.439045
crm_katalogsortpart	demo	2015-02-01 16:02:41.439045
crm_wvhistory2	demo	2015-02-01 16:02:41.439045
\.


--
-- Data for Name: sepa_export; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY sepa_export (id, employee_id, executed, closed, itime, vc) FROM stdin;
\.


--
-- Name: sepa_export_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('sepa_export_id_seq', 1, false);


--
-- Data for Name: sepa_export_items; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY sepa_export_items (id, sepa_export_id, ap_id, chart_id, amount, reference, requested_execution_date, executed, execution_date, our_iban, our_bic, vc_iban, vc_bic, end_to_end_id, our_depositor, vc_depositor, ar_id, vc_mandator_id, vc_mandate_date_of_signature) FROM stdin;
\.


--
-- Data for Name: shipto; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY shipto (trans_id, shiptoname, shiptodepartment_1, shiptodepartment_2, shiptostreet, shiptozipcode, shiptocity, shiptocountry, shiptocontact, shiptophone, shiptofax, shiptoemail, itime, mtime, module, shipto_id, shiptocp_gender, shiptoowener, shiptoemployee, shiptobland) FROM stdin;
\.


--
-- Data for Name: status; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY status (trans_id, formname, printed, emailed, spoolfile, chart_id, itime, mtime, id) FROM stdin;
\.


--
-- Name: status_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('status_id_seq', 1, false);


--
-- Data for Name: tax; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY tax (chart_id, rate, taxnumber, taxkey, taxdescription, itime, mtime, id, chart_categories) FROM stdin;
263	0.16000	1773	13	Steuerpflichtige EG-Lieferung zum vollen Steuersatz	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	385	I
864	0.19000	1774	13	Steuerpflichtige EG-Lieferung zum vollen Steuersatz	2015-02-01 15:52:37.870408	2015-02-01 15:52:41.003271	865	I
258	0.07000	1572	18	Steuerpflichtiger innergem. Erwerb zum ermäßigten Steuersatz	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	386	E
258	0.16000	1573	19	Steuerpflichtiger innergem. Erwerb zum vollen Steuersatz	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	387	E
863	0.19000	1574	19	Steuerpflichtiger innergem. Erwerb zum vollen Steuersatz	2015-02-01 15:52:37.870408	2015-02-01 15:52:41.003271	866	E
\N	0.00000	\N	0	Keine Steuer	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	0	ALQCIE
\N	0.00000	\N	1	USt-frei	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	377	ALQCIE
194	0.07000	1771	2	Umsatzsteuer	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	378	I
195	0.16000	1775	3	Umsatzsteuer	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	379	I
775	0.19000	1776	3	Umsatzsteuer	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	777	I
259	0.07000	1571	8	Vorsteuer	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	380	E
261	0.16000	1575	9	Vorsteuer	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	381	E
776	0.19000	1576	9	Vorsteuer	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	778	E
196	0.00000	1767	10	Im anderen EU-Staat steuerpflichtige Lieferung	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	382	I
\N	0.00000	\N	11	Steuerfreie innergem. Lieferung an Abnehmer mit Id.-Nr.	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	383	I
262	0.07000	1772	12	Steuerpflichtige EG-Lieferung zum ermäßigten Steuersatz	2015-02-01 15:29:33.757477	2015-02-01 15:52:41.003271	384	I
\.


--
-- Data for Name: tax_zones; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY tax_zones (id, description, sortkey, obsolete) FROM stdin;
1	EU mit USt-ID Nummer	2	f
2	EU ohne USt-ID Nummer	3	f
3	Außerhalb EU	4	f
4	Inland	1	f
\.


--
-- Data for Name: taxkeys; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY taxkeys (id, chart_id, tax_id, taxkey_id, pos_ustva, startdate) FROM stdin;
657	343	378	2	86	1970-01-01
658	319	378	2	86	1970-01-01
659	303	378	2	86	1970-01-01
660	176	378	2	86	1970-01-01
661	172	378	2	86	1970-01-01
662	165	378	2	86	1970-01-01
663	163	378	2	86	1970-01-01
664	161	378	2	86	1970-01-01
665	158	378	2	86	1970-01-01
666	154	378	2	86	1970-01-01
667	88	378	2	86	1970-01-01
668	362	379	3	51	1970-01-01
669	353	379	3	51	1970-01-01
670	344	379	3	51	1970-01-01
671	320	379	3	51	1970-01-01
672	302	379	3	51	1970-01-01
673	300	379	3	51	1970-01-01
674	276	379	3	51	1970-01-01
675	185	379	3	51	1970-01-01
676	184	379	3	51	1970-01-01
677	177	379	3	51	1970-01-01
678	175	379	3	51	1970-01-01
679	171	379	3	51	1970-01-01
680	168	379	3	\N	1970-01-01
681	167	379	3	51	1970-01-01
682	166	379	3	51	1970-01-01
683	164	379	3	51	1970-01-01
684	162	379	3	51	1970-01-01
685	160	379	3	51	1970-01-01
686	159	379	3	51	1970-01-01
687	157	379	3	51	1970-01-01
689	155	379	3	51	1970-01-01
690	153	379	3	51	1970-01-01
691	293	380	8	\N	1970-01-01
692	133	380	8	\N	1970-01-01
693	106	380	8	\N	1970-01-01
694	101	380	8	\N	1970-01-01
695	46	380	8	\N	1970-01-01
696	44	380	8	\N	1970-01-01
697	42	380	8	\N	1970-01-01
698	36	380	8	\N	1970-01-01
699	335	381	9	\N	1970-01-01
700	294	381	9	\N	1970-01-01
701	292	381	9	\N	1970-01-01
688	156	379	3	51	1970-01-01
702	281	381	9	0	1970-01-01
703	270	381	9	\N	1970-01-01
704	269	381	9	\N	1970-01-01
705	247	381	9	\N	1970-01-01
706	143	381	9	\N	1970-01-01
707	142	381	9	\N	1970-01-01
708	140	381	9	\N	1970-01-01
709	139	381	9	\N	1970-01-01
710	138	381	9	\N	1970-01-01
711	137	381	9	\N	1970-01-01
712	136	381	9	\N	1970-01-01
713	134	381	9	\N	1970-01-01
714	132	381	9	\N	1970-01-01
715	131	381	9	\N	1970-01-01
716	130	381	9	\N	1970-01-01
717	127	381	9	\N	1970-01-01
718	126	381	9	\N	1970-01-01
719	118	381	9	\N	1970-01-01
720	117	381	9	\N	1970-01-01
721	116	381	9	\N	1970-01-01
722	105	381	9	\N	1970-01-01
723	100	381	9	\N	1970-01-01
724	98	381	9	\N	1970-01-01
725	97	381	9	\N	1970-01-01
726	96	381	9	\N	1970-01-01
727	95	381	9	\N	1970-01-01
728	94	381	9	\N	1970-01-01
729	93	381	9	\N	1970-01-01
730	92	381	9	\N	1970-01-01
731	89	381	9	\N	1970-01-01
732	85	381	9	\N	1970-01-01
733	75	381	9	\N	1970-01-01
734	74	381	9	\N	1970-01-01
735	73	381	9	\N	1970-01-01
736	72	381	9	\N	1970-01-01
737	71	381	9	\N	1970-01-01
738	69	381	9	\N	1970-01-01
739	68	381	9	\N	1970-01-01
740	47	381	9	\N	1970-01-01
741	45	381	9	\N	1970-01-01
742	43	381	9	\N	1970-01-01
743	37	381	9	\N	1970-01-01
744	35	381	9	\N	1970-01-01
745	11	381	9	\N	1970-01-01
746	10	381	9	\N	1970-01-01
747	9	381	9	\N	1970-01-01
748	8	381	9	\N	1970-01-01
749	7	381	9	\N	1970-01-01
750	6	381	9	\N	1970-01-01
751	5	381	9	\N	1970-01-01
752	4	381	9	\N	1970-01-01
753	3	381	9	\N	1970-01-01
754	2	381	9	\N	1970-01-01
755	1	381	9	\N	1970-01-01
756	196	382	10	\N	1970-01-01
757	152	382	10	\N	1970-01-01
758	345	384	12	86	1970-01-01
759	150	384	12	86	1970-01-01
760	346	385	13	51	1970-01-01
761	151	385	13	51	1970-01-01
762	40	387	19	94	1970-01-01
781	156	777	3	\N	2007-01-01
789	168	777	3	\N	2007-01-01
802	1	778	9	\N	2007-01-01
803	2	778	9	\N	2007-01-01
804	3	778	9	\N	2007-01-01
805	4	778	9	\N	2007-01-01
806	5	778	9	\N	2007-01-01
807	6	778	9	\N	2007-01-01
808	7	778	9	\N	2007-01-01
809	8	778	9	\N	2007-01-01
810	9	778	9	\N	2007-01-01
811	10	778	9	\N	2007-01-01
812	11	778	9	\N	2007-01-01
813	35	778	9	\N	2007-01-01
814	37	778	9	\N	2007-01-01
815	43	778	9	\N	2007-01-01
816	45	778	9	\N	2007-01-01
817	47	778	9	\N	2007-01-01
818	68	778	9	\N	2007-01-01
819	69	778	9	\N	2007-01-01
820	71	778	9	\N	2007-01-01
821	72	778	9	\N	2007-01-01
822	73	778	9	\N	2007-01-01
823	74	778	9	\N	2007-01-01
824	75	778	9	\N	2007-01-01
825	85	778	9	\N	2007-01-01
826	89	778	9	\N	2007-01-01
827	92	778	9	\N	2007-01-01
828	93	778	9	\N	2007-01-01
829	94	778	9	\N	2007-01-01
830	95	778	9	\N	2007-01-01
831	96	778	9	\N	2007-01-01
832	97	778	9	\N	2007-01-01
833	98	778	9	\N	2007-01-01
834	100	778	9	\N	2007-01-01
835	105	778	9	\N	2007-01-01
836	116	778	9	\N	2007-01-01
837	117	778	9	\N	2007-01-01
838	118	778	9	\N	2007-01-01
839	126	778	9	\N	2007-01-01
840	127	778	9	\N	2007-01-01
841	130	778	9	\N	2007-01-01
842	131	778	9	\N	2007-01-01
843	132	778	9	\N	2007-01-01
844	134	778	9	\N	2007-01-01
845	136	778	9	\N	2007-01-01
846	137	778	9	\N	2007-01-01
847	138	778	9	\N	2007-01-01
848	139	778	9	\N	2007-01-01
849	140	778	9	\N	2007-01-01
850	142	778	9	\N	2007-01-01
851	143	778	9	\N	2007-01-01
852	247	778	9	\N	2007-01-01
853	269	778	9	\N	2007-01-01
854	270	778	9	\N	2007-01-01
855	281	778	9	0	2007-01-01
856	292	778	9	\N	2007-01-01
857	294	778	9	\N	2007-01-01
858	335	778	9	\N	2007-01-01
388	375	0	0	\N	1970-01-01
779	153	777	3	81	2007-01-01
780	155	777	3	81	2007-01-01
782	157	777	3	81	2007-01-01
389	374	0	0	\N	1970-01-01
390	373	0	0	\N	1970-01-01
391	372	0	0	\N	1970-01-01
392	371	0	0	\N	1970-01-01
393	370	0	0	\N	1970-01-01
394	369	0	0	\N	1970-01-01
395	368	0	0	\N	1970-01-01
396	367	0	0	\N	1970-01-01
397	366	0	0	\N	1970-01-01
398	365	0	0	\N	1970-01-01
399	364	0	0	\N	1970-01-01
400	363	0	0	\N	1970-01-01
401	361	0	0	\N	1970-01-01
402	360	0	0	\N	1970-01-01
403	359	0	0	\N	1970-01-01
404	358	0	0	\N	1970-01-01
405	357	0	0	\N	1970-01-01
406	356	0	0	\N	1970-01-01
407	355	0	0	\N	1970-01-01
408	354	0	0	\N	1970-01-01
409	352	0	0	\N	1970-01-01
410	351	0	0	\N	1970-01-01
411	350	0	0	\N	1970-01-01
412	349	0	0	\N	1970-01-01
413	348	0	0	\N	1970-01-01
414	347	0	0	\N	1970-01-01
415	342	0	0	\N	1970-01-01
416	341	0	0	\N	1970-01-01
417	340	0	0	\N	1970-01-01
418	338	0	0	\N	1970-01-01
419	337	0	0	\N	1970-01-01
420	336	0	0	\N	1970-01-01
421	334	0	0	\N	1970-01-01
422	333	0	0	\N	1970-01-01
423	332	0	0	\N	1970-01-01
424	331	0	0	\N	1970-01-01
425	330	0	0	\N	1970-01-01
426	329	0	0	\N	1970-01-01
427	328	0	0	\N	1970-01-01
428	327	0	0	\N	1970-01-01
429	326	0	0	\N	1970-01-01
430	325	0	0	\N	1970-01-01
431	324	0	0	\N	1970-01-01
432	323	0	0	\N	1970-01-01
433	322	0	0	\N	1970-01-01
434	321	0	0	\N	1970-01-01
435	318	0	0	\N	1970-01-01
436	317	0	0	\N	1970-01-01
437	316	0	0	\N	1970-01-01
438	315	0	0	\N	1970-01-01
439	314	0	0	\N	1970-01-01
440	313	0	0	\N	1970-01-01
441	312	0	0	\N	1970-01-01
442	311	0	0	\N	1970-01-01
443	310	0	0	\N	1970-01-01
444	309	0	0	\N	1970-01-01
445	308	0	0	\N	1970-01-01
446	307	0	0	\N	1970-01-01
447	306	0	0	\N	1970-01-01
448	305	0	0	\N	1970-01-01
449	304	0	0	\N	1970-01-01
450	301	0	0	\N	1970-01-01
451	299	0	0	0	1970-01-01
452	298	0	0	\N	1970-01-01
453	297	0	0	\N	1970-01-01
454	296	0	0	\N	1970-01-01
455	295	0	0	\N	1970-01-01
456	291	0	0	\N	1970-01-01
457	290	0	0	62	1970-01-01
458	289	0	0	\N	1970-01-01
459	288	0	0	\N	1970-01-01
460	287	0	0	\N	1970-01-01
461	286	0	0	\N	1970-01-01
464	283	0	0	\N	1970-01-01
465	282	0	0	\N	1970-01-01
466	280	0	0	0	1970-01-01
467	279	0	0	0	1970-01-01
468	278	0	0	0	1970-01-01
469	277	0	0	0	1970-01-01
470	275	0	0	\N	1970-01-01
471	274	0	0	\N	1970-01-01
472	273	0	0	\N	1970-01-01
473	272	0	0	\N	1970-01-01
474	271	0	0	\N	1970-01-01
475	268	0	0	\N	1970-01-01
476	267	0	0	\N	1970-01-01
477	266	0	0	0	1970-01-01
478	265	0	0	\N	1970-01-01
479	264	0	0	\N	1970-01-01
480	263	0	0	\N	1970-01-01
481	262	0	0	\N	1970-01-01
482	261	0	0	66	1970-01-01
483	260	0	0	61	1970-01-01
484	259	0	0	66	1970-01-01
485	258	0	0	61	1970-01-01
486	257	0	0	\N	1970-01-01
487	256	0	0	0	1970-01-01
488	255	0	0	0	1970-01-01
489	254	0	0	\N	1970-01-01
490	253	0	0	\N	1970-01-01
491	252	0	0	\N	1970-01-01
492	251	0	0	0	1970-01-01
493	250	0	0	0	1970-01-01
494	249	0	0	0	1970-01-01
495	248	0	0	\N	1970-01-01
496	246	0	0	\N	1970-01-01
497	245	0	0	\N	1970-01-01
498	244	0	0	\N	1970-01-01
499	243	0	0	\N	1970-01-01
500	242	0	0	\N	1970-01-01
501	241	0	0	\N	1970-01-01
502	240	0	0	\N	1970-01-01
503	239	0	0	\N	1970-01-01
504	238	0	0	\N	1970-01-01
505	237	0	0	\N	1970-01-01
506	236	0	0	\N	1970-01-01
507	235	0	0	\N	1970-01-01
508	234	0	0	\N	1970-01-01
509	233	0	0	\N	1970-01-01
510	232	0	0	0	1970-01-01
511	231	0	0	\N	1970-01-01
512	230	0	0	\N	1970-01-01
513	229	0	0	\N	1970-01-01
514	228	0	0	\N	1970-01-01
515	227	0	0	\N	1970-01-01
516	226	0	0	\N	1970-01-01
517	225	0	0	\N	1970-01-01
518	224	0	0	0	1970-01-01
519	223	0	0	\N	1970-01-01
520	222	0	0	\N	1970-01-01
521	221	0	0	\N	1970-01-01
522	220	0	0	\N	1970-01-01
523	219	0	0	0	1970-01-01
524	218	0	0	0	1970-01-01
525	217	0	0	0	1970-01-01
526	216	0	0	91	1970-01-01
527	215	0	0	\N	1970-01-01
528	214	0	0	\N	1970-01-01
529	213	0	0	\N	1970-01-01
530	212	0	0	\N	1970-01-01
531	211	0	0	\N	1970-01-01
532	210	0	0	\N	1970-01-01
533	209	0	0	\N	1970-01-01
534	208	0	0	\N	1970-01-01
535	207	0	0	\N	1970-01-01
536	206	0	0	\N	1970-01-01
537	205	0	0	\N	1970-01-01
538	204	0	0	\N	1970-01-01
539	203	0	0	\N	1970-01-01
540	202	0	0	\N	1970-01-01
541	201	0	0	\N	1970-01-01
542	200	0	0	\N	1970-01-01
543	199	0	0	\N	1970-01-01
544	198	0	0	\N	1970-01-01
545	197	0	0	\N	1970-01-01
548	193	0	0	\N	1970-01-01
549	192	0	0	\N	1970-01-01
550	191	0	0	\N	1970-01-01
551	190	0	0	\N	1970-01-01
552	189	0	0	\N	1970-01-01
553	188	0	0	\N	1970-01-01
554	187	0	0	\N	1970-01-01
555	186	0	0	\N	1970-01-01
556	183	0	0	\N	1970-01-01
557	182	0	0	\N	1970-01-01
558	181	0	0	\N	1970-01-01
559	180	0	0	\N	1970-01-01
560	179	0	0	\N	1970-01-01
561	178	0	0	\N	1970-01-01
562	174	0	0	\N	1970-01-01
563	173	0	0	\N	1970-01-01
564	170	0	0	\N	1970-01-01
565	169	0	0	\N	1970-01-01
566	149	0	0	\N	1970-01-01
567	148	0	0	\N	1970-01-01
568	147	0	0	\N	1970-01-01
569	146	0	0	\N	1970-01-01
570	145	0	0	41	1970-01-01
571	144	0	0	48	1970-01-01
572	141	0	0	\N	1970-01-01
573	135	0	0	\N	1970-01-01
574	129	0	0	\N	1970-01-01
575	128	0	0	\N	1970-01-01
576	125	0	0	\N	1970-01-01
577	124	0	0	\N	1970-01-01
578	123	0	0	\N	1970-01-01
579	122	0	0	\N	1970-01-01
580	121	0	0	\N	1970-01-01
581	120	0	0	\N	1970-01-01
582	119	0	0	\N	1970-01-01
583	115	0	0	\N	1970-01-01
584	114	0	0	\N	1970-01-01
585	113	0	0	\N	1970-01-01
586	112	0	0	\N	1970-01-01
587	111	0	0	\N	1970-01-01
588	110	0	0	\N	1970-01-01
589	109	0	0	\N	1970-01-01
590	108	0	0	\N	1970-01-01
591	107	0	0	\N	1970-01-01
592	104	0	0	\N	1970-01-01
593	103	0	0	\N	1970-01-01
463	284	0	0	63	1970-01-01
547	194	378	2	861	1970-01-01
594	102	0	0	\N	1970-01-01
595	99	0	0	\N	1970-01-01
596	91	0	0	\N	1970-01-01
597	90	0	0	\N	1970-01-01
598	87	0	0	\N	1970-01-01
599	86	0	0	0	1970-01-01
600	84	0	0	\N	1970-01-01
601	83	0	0	\N	1970-01-01
602	82	0	0	\N	1970-01-01
603	81	0	0	\N	1970-01-01
604	80	0	0	\N	1970-01-01
605	79	0	0	\N	1970-01-01
606	78	0	0	\N	1970-01-01
607	77	0	0	\N	1970-01-01
608	76	0	0	\N	1970-01-01
609	339	0	0	\N	1970-01-01
610	70	0	0	\N	1970-01-01
611	67	0	0	\N	1970-01-01
612	66	0	0	\N	1970-01-01
613	65	0	0	\N	1970-01-01
614	64	0	0	\N	1970-01-01
615	63	0	0	\N	1970-01-01
616	62	0	0	\N	1970-01-01
617	61	0	0	\N	1970-01-01
618	60	0	0	\N	1970-01-01
619	59	0	0	\N	1970-01-01
620	58	0	0	\N	1970-01-01
621	57	0	0	\N	1970-01-01
622	56	0	0	\N	1970-01-01
623	55	0	0	\N	1970-01-01
624	54	0	0	\N	1970-01-01
625	53	0	0	\N	1970-01-01
626	52	0	0	\N	1970-01-01
627	51	0	0	\N	1970-01-01
628	50	0	0	\N	1970-01-01
629	49	0	0	\N	1970-01-01
630	48	0	0	\N	1970-01-01
631	41	0	0	91	1970-01-01
632	39	0	0	97	1970-01-01
633	38	0	0	93	1970-01-01
634	34	0	0	\N	1970-01-01
635	33	0	0	\N	1970-01-01
636	32	0	0	\N	1970-01-01
637	31	0	0	\N	1970-01-01
638	30	0	0	\N	1970-01-01
639	29	0	0	\N	1970-01-01
640	28	0	0	\N	1970-01-01
641	27	0	0	\N	1970-01-01
642	26	0	0	\N	1970-01-01
643	25	0	0	\N	1970-01-01
644	24	0	0	\N	1970-01-01
645	23	0	0	\N	1970-01-01
646	22	0	0	\N	1970-01-01
647	21	0	0	\N	1970-01-01
648	20	0	0	\N	1970-01-01
649	19	0	0	\N	1970-01-01
650	18	0	0	\N	1970-01-01
651	17	0	0	\N	1970-01-01
652	16	0	0	\N	1970-01-01
653	15	0	0	\N	1970-01-01
654	14	0	0	\N	1970-01-01
655	13	0	0	\N	1970-01-01
656	12	0	0	\N	1970-01-01
783	159	777	3	81	2007-01-01
784	160	777	3	81	2007-01-01
785	162	777	3	81	2007-01-01
786	164	777	3	81	2007-01-01
787	166	777	3	81	2007-01-01
788	167	777	3	81	2007-01-01
790	171	777	3	81	2007-01-01
791	175	777	3	81	2007-01-01
792	177	777	3	81	2007-01-01
793	184	777	3	81	2007-01-01
794	185	777	3	81	2007-01-01
795	276	777	3	81	2007-01-01
796	300	777	3	81	2007-01-01
797	302	777	3	81	2007-01-01
798	320	777	3	81	2007-01-01
799	344	777	3	81	2007-01-01
800	353	777	3	81	2007-01-01
801	362	777	3	81	2007-01-01
546	195	0	0	511	1970-01-01
861	775	777	0	811	2007-01-01
867	776	778	9	66	1970-01-01
868	863	866	19	61	1970-01-01
869	864	0	0	891	2007-01-01
462	285	0	0	67	1970-01-01
870	862	0	0	66	1970-01-01
872	775	777	0	36	1970-01-01
873	195	379	0	36	2007-01-01
\.


--
-- Data for Name: taxzone_charts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY taxzone_charts (id, taxzone_id, buchungsgruppen_id, income_accno_id, expense_accno_id, itime) FROM stdin;
3	1	860	145	41	2015-02-01 15:52:41.385535
4	1	859	152	41	2015-02-01 15:52:41.385535
5	2	860	150	38	2015-02-01 15:52:41.385535
6	2	859	151	39	2015-02-01 15:52:41.385535
7	3	860	144	216	2015-02-01 15:52:41.385535
8	3	859	144	216	2015-02-01 15:52:41.385535
1	4	860	88	36	2015-02-01 15:52:41.385535
2	4	859	184	37	2015-02-01 15:52:41.385535
\.


--
-- Name: taxzone_charts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('taxzone_charts_id_seq', 8, true);


--
-- Data for Name: telcall; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY telcall (id, termin_id, cause, caller_id, calldate, c_long, employee, kontakt, "inout", bezug, dokument) FROM stdin;
\.


--
-- Data for Name: telcallhistory; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY telcallhistory (id, orgid, cause, caller_id, calldate, c_long, employee, kontakt, bezug, dokument, chgid, grund, datum) FROM stdin;
\.


--
-- Data for Name: telnr; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY telnr (id, tabelle, nummer) FROM stdin;
891	C	3341364419
891	C	1757880999
\.


--
-- Data for Name: tempcsvdata; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY tempcsvdata (uid, csvdaten, id) FROM stdin;
\.


--
-- Data for Name: termdate; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY termdate (id, termid, datum, jahr, kw, tag, monat, idx) FROM stdin;
\.


--
-- Data for Name: termincat; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY termincat (catid, catname, sorder, ccolor) FROM stdin;
\.


--
-- Data for Name: termine; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY termine (id, cause, c_cause, start, stop, repeat, ft, starttag, stoptag, startzeit, stopzeit, privat, uid, kategorie, location, syncid) FROM stdin;
\.


--
-- Data for Name: terminmember; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY terminmember (termin, member, tabelle) FROM stdin;
\.


--
-- Data for Name: timetrack; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY timetrack (id, fid, tab, ttname, budget, ttdescription, startdate, stopdate, aim, active, uid) FROM stdin;
\.


--
-- Data for Name: todo_user_config; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY todo_user_config (employee_id, show_after_login, show_follow_ups, show_follow_ups_login, show_overdue_sales_quotations, show_overdue_sales_quotations_login, id) FROM stdin;
\.


--
-- Name: todo_user_config_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('todo_user_config_id_seq', 1, false);


--
-- Data for Name: transfer_type; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY transfer_type (id, direction, description, sortkey, itime, mtime) FROM stdin;
879	in	stock	1	2015-02-01 15:52:38.295851	\N
880	in	found	2	2015-02-01 15:52:38.295851	\N
881	in	correction	3	2015-02-01 15:52:38.295851	\N
882	out	used	4	2015-02-01 15:52:38.295851	\N
883	out	disposed	5	2015-02-01 15:52:38.295851	\N
884	out	back	6	2015-02-01 15:52:38.295851	\N
885	out	missing	7	2015-02-01 15:52:38.295851	\N
886	out	correction	9	2015-02-01 15:52:38.295851	\N
887	transfer	transfer	10	2015-02-01 15:52:38.295851	\N
888	transfer	correction	11	2015-02-01 15:52:38.295851	\N
889	out	shipped	12	2015-02-01 15:52:38.371609	\N
\.


--
-- Data for Name: translation; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY translation (parts_id, language_id, translation, itime, mtime, longdescription, id) FROM stdin;
\.


--
-- Name: translation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('translation_id_seq', 1, false);


--
-- Data for Name: trigger_information; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY trigger_information (id, key, value) FROM stdin;
\.


--
-- Name: trigger_information_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('trigger_information_id_seq', 1, false);


--
-- Data for Name: tt_event; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY tt_event (id, ttid, uid, ttevent, ttstart, ttstop, cleared) FROM stdin;
\.


--
-- Data for Name: tt_parts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY tt_parts (eid, qty, parts_id, parts_txt) FROM stdin;
\.


--
-- Data for Name: units; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY units (name, base_unit, factor, type, sortkey, id) FROM stdin;
Stck	\N	\N	dimension	1	1
psch	\N	0.00000	service	2	2
Tag	Std	8.00000	service	3	3
Std	min	60.00000	service	4	4
min	\N	0.00000	service	5	5
t	kg	1000.00000	dimension	6	6
kg	g	1000.00000	dimension	7	7
g	mg	1000.00000	dimension	8	8
mg	\N	\N	dimension	9	9
L	ml	1000.00000	dimension	10	10
ml	\N	\N	dimension	11	11
\.


--
-- Name: units_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('units_id_seq', 11, true);


--
-- Data for Name: units_language; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY units_language (unit, language_id, localized, localized_plural, id) FROM stdin;
\.


--
-- Name: units_language_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('units_language_id_seq', 1, false);


--
-- Data for Name: vendor; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY vendor (id, name, department_1, department_2, street, zipcode, city, country, contact, phone, fax, homepage, email, notes, terms, taxincluded, vendornumber, cc, bcc, business_id, taxnumber, discount, creditlimit, account_number, bank_code, bank, language, itime, mtime, obsolete, username, user_password, salesman_id, v_customer_id, language_id, payment_id, taxzone_id, greeting, ustid, iban, bic, direct_debit, depositor, delivery_term_id, currency_id, owener, employee, kundennummer, sw, branche, grafik, sonder, bland, lead, leadsrc, konzern, headcount) FROM stdin;
\.


--
-- Data for Name: warehouse; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY warehouse (id, description, itime, mtime, sortkey, invalid) FROM stdin;
\.


--
-- Data for Name: wiedervorlage; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY wiedervorlage (id, initdate, changedate, finishdate, cause, descript, document, status, kontakt, employee, gruppe, initemployee, kontaktid, kontakttab, tellid) FROM stdin;
\.


--
-- Data for Name: wissencategorie; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY wissencategorie (id, name, hauptgruppe, kdhelp) FROM stdin;
\.


--
-- Data for Name: wissencontent; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY wissencontent (id, initdate, content, employee, owener, version, categorie) FROM stdin;
\.


SET search_path = tax, pg_catalog;

--
-- Data for Name: report_categories; Type: TABLE DATA; Schema: tax; Owner: postgres
--

COPY report_categories (id, description, subdescription) FROM stdin;
0		
1	Lieferungen und sonstige Leistungen	(einschließlich unentgeltlicher Wertabgaben)
2	Innergemeinschaftliche Erwerbe	
3	Ergänzende Angaben zu Umsätzen	
99	Summe	
\.


--
-- Data for Name: report_headings; Type: TABLE DATA; Schema: tax; Owner: postgres
--

COPY report_headings (id, category_id, type, description, subdescription) FROM stdin;
0	0			
1	1	received	Steuerfreie Umsätze mit Vorsteuerabzug	
2	1	recieved	Steuerfreie Umsätze ohne Vorsteuerabzug	
3	1	recieved	Steuerpflichtige Umsätze	(Lieferungen und sonstige Leistungen einschl. unentgeltlicher Wertabgaben)
4	2	recieved	Steuerfreie innergemeinschaftliche Erwerbe	
5	2	recieved	Steuerpflichtige innergemeinschaftliche Erwerbe	
6	3	recieved	Umsätze, für die als Leistungsempfänger die Steuer nach § 13b Abs. 2 UStG geschuldet wird	
66	3	recieved		
7	3	paied	Abziehbare Vorsteuerbeträge	
8	3	paied	Andere Steuerbeträge	
99	99		Summe	
\.


--
-- Data for Name: report_variables; Type: TABLE DATA; Schema: tax; Owner: postgres
--

COPY report_variables (id, "position", heading_id, description, taxbase, dec_places, valid_from) FROM stdin;
0	keine	0	< < < keine UStVa Position > > >			1970-01-01
1	41	1	Innergemeinschaftliche Lieferungen (§ 4 Nr. 1 Buchst. b UStG) an Abnehmer mit USt-IdNr.	0	0	1970-01-01
2	44	1	neuer Fahrzeuge an Abnehmer ohne USt-IdNr.	0	0	1970-01-01
3	49	1	neuer Fahrzeuge außerhalb eines Unternehmens (§ 2a UStG)	0	0	1970-01-01
4	43	1	Weitere steuerfreie Umsätze mit Vorsteuerabzug	0	0	1970-01-01
5	48	2	Umsätze nach § 4 Nr. 8 bis 28 UStG	0	0	1970-01-01
6	51	3	zum Steuersatz von 16 %	0	0	1970-01-01
7	511	3		6	2	1970-01-01
8	81	3	zum Steuersatz von 19 %	0	0	1970-01-01
9	811	3		8	2	1970-01-01
10	86	3	zum Steuersatz von 7 %	0	0	1970-01-01
11	861	3		10	2	1970-01-01
12	35	3	Umsätze, die anderen Steuersätzen unterliegen	0	0	1970-01-01
13	36	3		12	2	1970-01-01
14	77	3	Lieferungen in das übrige Gemeinschaftsgebiet an Abnehmer mit USt-IdNr.	0	0	1970-01-01
15	76	3	Umsätze, für die eine Steuer nach § 24 UStG zu entrichten ist	0	0	1970-01-01
16	80	3		15	2	1970-01-01
17	91	4	Erwerbe nach § 4b UStG	0	0	1970-01-01
18	97	5	zum Steuersatz von 16 %	0	0	1970-01-01
19	971	5		18	2	1970-01-01
20	89	5	zum Steuersatz von 19 %	0	0	1970-01-01
21	891	5		20	2	1970-01-01
22	93	5	zum Steuersatz von 7 %	0	0	1970-01-01
23	931	5		22	2	1970-01-01
24	95	5	zu anderen Steuersätzen	0	0	1970-01-01
25	98	5		24	2	1970-01-01
26	94	5	neuer Fahrzeuge von Lieferern ohne USt-IdNr. zum allgemeinen Steuersatz	0	0	1970-01-01
27	96	5		26	2	1970-01-01
28	42	66	Lieferungen des ersten Abnehmers bei innergemeinschaftlichen Dreiecksgeschäften (§ 25b Abs. 2 UStG)	0	0	1970-01-01
29	60	66	Steuerpflichtige Umsätze im Sinne des § 13b Abs. 1 Satz 1 Nr. 1 bis 5 UStG, für die der Leistungsempfänger die Steuer schuldet	0	0	1970-01-01
30	45	66	Nicht steuerbare Umsätze (Leistungsort nicht im Inland)	0	0	1970-01-01
31	52	6	Leistungen eines im Ausland ansässigen Unternehmers (§ 13b Abs. 1 Satz 1 Nr. 1 und 5 UStG)	0	0	1970-01-01
32	53	6		31	2	1970-01-01
33	73	6	Lieferungen sicherungsübereigneter Gegenstände und Umsätze, die unter das GrEStG fallen (§ 13b Abs. 1 Satz 1 Nr. 2 und 3 UStG)	0	0	1970-01-01
34	74	6		33	2	1970-01-01
35	84	6	Bauleistungen eines im Inland ansässigen Unternehmers (§ 13b Abs. 1 Satz 1 Nr. 4 UStG)	0	0	1970-01-01
36	85	6		35	2	1970-01-01
37	65	6	Steuer infolge Wechsels der Besteuerungsform sowie Nachsteuer auf versteuerte Anzahlungen u. ä. wegen Steuersatzänderung		2	1970-01-01
38	66	7	Vorsteuerbeträge aus Rechnungen von anderen Unternehmern (§ 15 Abs. 1 Satz 1 Nr. 1 UStG), aus Leistungen im Sinne des § 13a Abs. 1 Nr. 6 UStG (§ 15 Abs. 1 Satz 1 Nr. 5 UStG) und aus innergemeinschaftlichen Dreiecksgeschäften (§ 25b Abs. 5 UStG)		2	1970-01-01
39	61	7	Vorsteuerbeträge aus dem innergemeinschaftlichen Erwerb von Gegenständen (§ 15 Abs. 1 Satz 1 Nr. 3 UStG)		2	1970-01-01
40	62	7	Entrichtete Einfuhrumsatzsteuer (§ 15 Abs. 1 Satz 1 Nr. 2 UStG)		2	1970-01-01
41	67	7	Vorsteuerbeträge aus Leistungen im Sinne des § 13b Abs. 1 UStG (§ 15 Abs. 1 Satz 1 Nr. 4 UStG)		2	1970-01-01
42	63	7	Vorsteuerbeträge, die nach allgemeinen Durchschnittssätzen berechnet sind (§§ 23 und 23a UStG)		2	1970-01-01
43	64	7	Berichtigung des Vorsteuerabzugs (§ 15a UStG)		2	1970-01-01
44	59	7	Vorsteuerabzug für innergemeinschaftliche Lieferungen neuer Fahrzeuge außerhalb eines Unternehmens (§ 2a UStG) sowie von Kleinunternehmern im Sinne des § 19 Abs. 1 UStG (§ 15 Abs. 4a UStG)		2	1970-01-01
45	69	8	in Rechnungen unrichtig oder unberechtigt ausgewiesene Steuerbeträge (§ 14c UStG) sowie Steuerbeträge, die nach § 4 Nr. 4a Satz 1 Buchst. a Satz 2, § 6a Abs. 4 Satz 2, § 17 Abs. 1 Satz 6 oder § 25b Abs. 2 UStG geschuldet werden		2	1970-01-01
46	39	8	Anrechnung (Abzug) der festgesetzten Sondervorauszahlung für Dauerfristverlängerung (nur auszufüllen in der letzten Voranmeldung des Besteuerungszeitraums, in der Regel Dezember)		2	1970-01-01
47	21	66	Nicht steuerbare sonstige Leistungen gem. § 18b Satz 1 Nr. 2 UStG	0	0	2010-01-01
48	46	6	Im Inland steuerpflichtige sonstige Leistungen von im übrigen Gemeinschaftsgebiet ansässigen Unternehmen (§13b Abs. 1 UStG)	0	0	2010-01-01
49	47	6		49	2	2010-01-01
50	83	8	Verbleibender Überschuss - bitte dem Betrag ein Minuszeichen voranstellen -	0	2	2010-01-01
\.


SET search_path = public, pg_catalog;

--
-- Name: acc_trans_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY acc_trans
    ADD CONSTRAINT acc_trans_pkey PRIMARY KEY (acc_trans_id);


--
-- Name: ap_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT ap_pkey PRIMARY KEY (id);


--
-- Name: ar_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_pkey PRIMARY KEY (id);


--
-- Name: assembly_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY assembly
    ADD CONSTRAINT assembly_pkey PRIMARY KEY (assembly_id);


--
-- Name: background_job_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY background_job_histories
    ADD CONSTRAINT background_job_histories_pkey PRIMARY KEY (id);


--
-- Name: background_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY background_jobs
    ADD CONSTRAINT background_jobs_pkey PRIMARY KEY (id);


--
-- Name: bank_accounts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY bank_accounts
    ADD CONSTRAINT bank_accounts_pkey PRIMARY KEY (id);


--
-- Name: bin_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY bin
    ADD CONSTRAINT bin_pkey PRIMARY KEY (id);


--
-- Name: buchungsgruppen_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY buchungsgruppen
    ADD CONSTRAINT buchungsgruppen_pkey PRIMARY KEY (id);


--
-- Name: business_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY business
    ADD CONSTRAINT business_pkey PRIMARY KEY (id);


--
-- Name: chart_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY chart
    ADD CONSTRAINT chart_pkey PRIMARY KEY (id);


--
-- Name: contacts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY contacts
    ADD CONSTRAINT contacts_pkey PRIMARY KEY (cp_id);


--
-- Name: csv_import_profile_settings_csv_import_profile_id_key_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY csv_import_profile_settings
    ADD CONSTRAINT csv_import_profile_settings_csv_import_profile_id_key_key UNIQUE (csv_import_profile_id, key);


--
-- Name: csv_import_profile_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY csv_import_profile_settings
    ADD CONSTRAINT csv_import_profile_settings_pkey PRIMARY KEY (id);


--
-- Name: csv_import_profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY csv_import_profiles
    ADD CONSTRAINT csv_import_profiles_pkey PRIMARY KEY (id);


--
-- Name: csv_import_report_rows_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY csv_import_report_rows
    ADD CONSTRAINT csv_import_report_rows_pkey PRIMARY KEY (id);


--
-- Name: csv_import_report_status_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY csv_import_report_status
    ADD CONSTRAINT csv_import_report_status_pkey PRIMARY KEY (id);


--
-- Name: csv_import_reports_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY csv_import_reports
    ADD CONSTRAINT csv_import_reports_pkey PRIMARY KEY (id);


--
-- Name: currencies_name_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY currencies
    ADD CONSTRAINT currencies_name_key UNIQUE (name);


--
-- Name: currencies_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY currencies
    ADD CONSTRAINT currencies_pkey PRIMARY KEY (id);


--
-- Name: custom_variable_config_partsgroups_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY custom_variable_config_partsgroups
    ADD CONSTRAINT custom_variable_config_partsgroups_pkey PRIMARY KEY (custom_variable_config_id, partsgroup_id);


--
-- Name: custom_variable_configs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY custom_variable_configs
    ADD CONSTRAINT custom_variable_configs_pkey PRIMARY KEY (id);


--
-- Name: custom_variables_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY custom_variables
    ADD CONSTRAINT custom_variables_pkey PRIMARY KEY (id);


--
-- Name: custom_variables_validity_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY custom_variables_validity
    ADD CONSTRAINT custom_variables_validity_pkey PRIMARY KEY (id);


--
-- Name: customer_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY customer
    ADD CONSTRAINT customer_pkey PRIMARY KEY (id);


--
-- Name: datev_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY datev
    ADD CONSTRAINT datev_pkey PRIMARY KEY (id);


--
-- Name: defaults_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY defaults
    ADD CONSTRAINT defaults_pkey PRIMARY KEY (id);


--
-- Name: delivery_order_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY delivery_order_items
    ADD CONSTRAINT delivery_order_items_pkey PRIMARY KEY (id);


--
-- Name: delivery_order_items_stock_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY delivery_order_items_stock
    ADD CONSTRAINT delivery_order_items_stock_pkey PRIMARY KEY (id);


--
-- Name: delivery_orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_pkey PRIMARY KEY (id);


--
-- Name: delivery_terms_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY delivery_terms
    ADD CONSTRAINT delivery_terms_pkey PRIMARY KEY (id);


--
-- Name: department_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY department
    ADD CONSTRAINT department_pkey PRIMARY KEY (id);


--
-- Name: drafts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY drafts
    ADD CONSTRAINT drafts_pkey PRIMARY KEY (id);


--
-- Name: dunning_config_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY dunning_config
    ADD CONSTRAINT dunning_config_pkey PRIMARY KEY (id);


--
-- Name: dunning_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY dunning
    ADD CONSTRAINT dunning_pkey PRIMARY KEY (id);


--
-- Name: employee_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY employee
    ADD CONSTRAINT employee_pkey PRIMARY KEY (id);


--
-- Name: event_category_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY event_category
    ADD CONSTRAINT event_category_pkey PRIMARY KEY (id);


--
-- Name: events_tmp_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY events
    ADD CONSTRAINT events_tmp_pkey PRIMARY KEY (id);


--
-- Name: exchangerate_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY exchangerate
    ADD CONSTRAINT exchangerate_pkey PRIMARY KEY (id);


--
-- Name: finanzamt_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY finanzamt
    ADD CONSTRAINT finanzamt_pkey PRIMARY KEY (id);


--
-- Name: follow_up_access_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY follow_up_access
    ADD CONSTRAINT follow_up_access_pkey PRIMARY KEY (id);


--
-- Name: follow_up_links_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY follow_up_links
    ADD CONSTRAINT follow_up_links_pkey PRIMARY KEY (id);


--
-- Name: follow_ups_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY follow_ups
    ADD CONSTRAINT follow_ups_pkey PRIMARY KEY (id);


--
-- Name: generic_translations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY generic_translations
    ADD CONSTRAINT generic_translations_pkey PRIMARY KEY (id);


--
-- Name: gl_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY gl
    ADD CONSTRAINT gl_pkey PRIMARY KEY (id);


--
-- Name: history_erp_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY history_erp
    ADD CONSTRAINT history_erp_pkey PRIMARY KEY (id);


--
-- Name: inventory_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT inventory_pkey PRIMARY KEY (id);


--
-- Name: invoice_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY invoice
    ADD CONSTRAINT invoice_pkey PRIMARY KEY (id);


--
-- Name: language_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY language
    ADD CONSTRAINT language_pkey PRIMARY KEY (id);


--
-- Name: makemodel_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY makemodel
    ADD CONSTRAINT makemodel_pkey PRIMARY KEY (id);


--
-- Name: notes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY notes
    ADD CONSTRAINT notes_pkey PRIMARY KEY (id);


--
-- Name: oe_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_pkey PRIMARY KEY (id);


--
-- Name: orderitems_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT orderitems_pkey PRIMARY KEY (id);


--
-- Name: parts_partnumber_key1; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY parts
    ADD CONSTRAINT parts_partnumber_key1 UNIQUE (partnumber);


--
-- Name: parts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY parts
    ADD CONSTRAINT parts_pkey PRIMARY KEY (id);


--
-- Name: partsgroup_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY partsgroup
    ADD CONSTRAINT partsgroup_pkey PRIMARY KEY (id);


--
-- Name: payment_terms_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY payment_terms
    ADD CONSTRAINT payment_terms_pkey PRIMARY KEY (id);


--
-- Name: periodic_invoices_configs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY periodic_invoices_configs
    ADD CONSTRAINT periodic_invoices_configs_pkey PRIMARY KEY (id);


--
-- Name: periodic_invoices_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY periodic_invoices
    ADD CONSTRAINT periodic_invoices_pkey PRIMARY KEY (id);


--
-- Name: price_factors_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY price_factors
    ADD CONSTRAINT price_factors_pkey PRIMARY KEY (id);


--
-- Name: price_rule_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY price_rule_items
    ADD CONSTRAINT price_rule_items_pkey PRIMARY KEY (id);


--
-- Name: price_rules_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY price_rules
    ADD CONSTRAINT price_rules_pkey PRIMARY KEY (id);


--
-- Name: pricegroup_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY pricegroup
    ADD CONSTRAINT pricegroup_pkey PRIMARY KEY (id);


--
-- Name: prices_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY prices
    ADD CONSTRAINT prices_pkey PRIMARY KEY (id);


--
-- Name: printers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY printers
    ADD CONSTRAINT printers_pkey PRIMARY KEY (id);


--
-- Name: project_participants_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY project_participants
    ADD CONSTRAINT project_participants_pkey PRIMARY KEY (id);


--
-- Name: project_phase_participants_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY project_phase_participants
    ADD CONSTRAINT project_phase_participants_pkey PRIMARY KEY (id);


--
-- Name: project_phases_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY project_phases
    ADD CONSTRAINT project_phases_pkey PRIMARY KEY (id);


--
-- Name: project_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY project
    ADD CONSTRAINT project_pkey PRIMARY KEY (id);


--
-- Name: project_projectnumber_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY project
    ADD CONSTRAINT project_projectnumber_key UNIQUE (projectnumber);


--
-- Name: project_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY project_roles
    ADD CONSTRAINT project_roles_pkey PRIMARY KEY (id);


--
-- Name: project_status_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY project_statuses
    ADD CONSTRAINT project_status_pkey PRIMARY KEY (id);


--
-- Name: project_types_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY project_types
    ADD CONSTRAINT project_types_pkey PRIMARY KEY (id);


--
-- Name: record_links_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY record_links
    ADD CONSTRAINT record_links_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_acceptance_statuses_name_description_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_acceptance_statuses
    ADD CONSTRAINT requirement_spec_acceptance_statuses_name_description_key UNIQUE (name, description);


--
-- Name: requirement_spec_acceptance_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_acceptance_statuses
    ADD CONSTRAINT requirement_spec_acceptance_statuses_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_complexities_description_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_complexities
    ADD CONSTRAINT requirement_spec_complexities_description_key UNIQUE (description);


--
-- Name: requirement_spec_complexities_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_complexities
    ADD CONSTRAINT requirement_spec_complexities_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_id_order_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_orders
    ADD CONSTRAINT requirement_spec_id_order_id_unique UNIQUE (requirement_spec_id, order_id);


--
-- Name: requirement_spec_item_dependencies_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_item_dependencies
    ADD CONSTRAINT requirement_spec_item_dependencies_pkey PRIMARY KEY (depending_item_id, depended_item_id);


--
-- Name: requirement_spec_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_items
    ADD CONSTRAINT requirement_spec_items_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_orders
    ADD CONSTRAINT requirement_spec_orders_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_parts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_parts
    ADD CONSTRAINT requirement_spec_parts_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_pictures_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_pictures
    ADD CONSTRAINT requirement_spec_pictures_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_predefined_texts_description_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_predefined_texts
    ADD CONSTRAINT requirement_spec_predefined_texts_description_key UNIQUE (description);


--
-- Name: requirement_spec_predefined_texts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_predefined_texts
    ADD CONSTRAINT requirement_spec_predefined_texts_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_risks_description_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_risks
    ADD CONSTRAINT requirement_spec_risks_description_key UNIQUE (description);


--
-- Name: requirement_spec_risks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_risks
    ADD CONSTRAINT requirement_spec_risks_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_statuses_name_description_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_statuses
    ADD CONSTRAINT requirement_spec_statuses_name_description_key UNIQUE (name, description);


--
-- Name: requirement_spec_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_statuses
    ADD CONSTRAINT requirement_spec_statuses_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_text_blocks_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_text_blocks
    ADD CONSTRAINT requirement_spec_text_blocks_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_types_description_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_types
    ADD CONSTRAINT requirement_spec_types_description_key UNIQUE (description);


--
-- Name: requirement_spec_types_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_types
    ADD CONSTRAINT requirement_spec_types_pkey PRIMARY KEY (id);


--
-- Name: requirement_spec_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_spec_versions
    ADD CONSTRAINT requirement_spec_versions_pkey PRIMARY KEY (id);


--
-- Name: requirement_specs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY requirement_specs
    ADD CONSTRAINT requirement_specs_pkey PRIMARY KEY (id);


--
-- Name: schema_info_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY schema_info
    ADD CONSTRAINT schema_info_pkey PRIMARY KEY (tag);


--
-- Name: sepa_export_items_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sepa_export_items
    ADD CONSTRAINT sepa_export_items_pkey PRIMARY KEY (id);


--
-- Name: sepa_export_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY sepa_export
    ADD CONSTRAINT sepa_export_pkey PRIMARY KEY (id);


--
-- Name: shipto_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY shipto
    ADD CONSTRAINT shipto_pkey PRIMARY KEY (shipto_id);


--
-- Name: status_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY status
    ADD CONSTRAINT status_pkey PRIMARY KEY (id);


--
-- Name: tax_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY tax
    ADD CONSTRAINT tax_pkey PRIMARY KEY (id);


--
-- Name: tax_zones_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY tax_zones
    ADD CONSTRAINT tax_zones_pkey PRIMARY KEY (id);


--
-- Name: taxkeys_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY taxkeys
    ADD CONSTRAINT taxkeys_pkey PRIMARY KEY (id);


--
-- Name: taxzone_charts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY taxzone_charts
    ADD CONSTRAINT taxzone_charts_pkey PRIMARY KEY (id);


--
-- Name: termincat_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY termincat
    ADD CONSTRAINT termincat_pkey PRIMARY KEY (catid);


--
-- Name: todo_user_config_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY todo_user_config
    ADD CONSTRAINT todo_user_config_pkey PRIMARY KEY (id);


--
-- Name: transfer_type_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY transfer_type
    ADD CONSTRAINT transfer_type_pkey PRIMARY KEY (id);


--
-- Name: translation_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY translation
    ADD CONSTRAINT translation_pkey PRIMARY KEY (id);


--
-- Name: trigger_information_key_value_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY trigger_information
    ADD CONSTRAINT trigger_information_key_value_key UNIQUE (key, value);


--
-- Name: trigger_information_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY trigger_information
    ADD CONSTRAINT trigger_information_pkey PRIMARY KEY (id);


--
-- Name: units_id_unique; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY units
    ADD CONSTRAINT units_id_unique UNIQUE (id);


--
-- Name: units_language_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY units_language
    ADD CONSTRAINT units_language_pkey PRIMARY KEY (id);


--
-- Name: units_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY units
    ADD CONSTRAINT units_pkey PRIMARY KEY (name);


--
-- Name: vendor_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY vendor
    ADD CONSTRAINT vendor_pkey PRIMARY KEY (id);


--
-- Name: warehouse_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY warehouse
    ADD CONSTRAINT warehouse_pkey PRIMARY KEY (id);


SET search_path = tax, pg_catalog;

--
-- Name: report_categorys_pkey; Type: CONSTRAINT; Schema: tax; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY report_categories
    ADD CONSTRAINT report_categorys_pkey PRIMARY KEY (id);


--
-- Name: report_headings_pkey; Type: CONSTRAINT; Schema: tax; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY report_headings
    ADD CONSTRAINT report_headings_pkey PRIMARY KEY (id);


--
-- Name: report_variables_pkey; Type: CONSTRAINT; Schema: tax; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY report_variables
    ADD CONSTRAINT report_variables_pkey PRIMARY KEY (id);


SET search_path = public, pg_catalog;

--
-- Name: acc_trans_chart_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX acc_trans_chart_id_key ON acc_trans USING btree (chart_id);


--
-- Name: acc_trans_source_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX acc_trans_source_key ON acc_trans USING btree (lower(source));


--
-- Name: acc_trans_trans_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX acc_trans_trans_id_key ON acc_trans USING btree (trans_id);


--
-- Name: acc_trans_transdate_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX acc_trans_transdate_key ON acc_trans USING btree (transdate);


--
-- Name: ap_employee_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ap_employee_id_key ON ap USING btree (employee_id);


--
-- Name: ap_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ap_id_key ON ap USING btree (id);


--
-- Name: ap_invnumber_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ap_invnumber_key ON ap USING btree (lower(invnumber));


--
-- Name: ap_ordnumber_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ap_ordnumber_key ON ap USING btree (lower(ordnumber));


--
-- Name: ap_quonumber_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ap_quonumber_key ON ap USING btree (lower(quonumber));


--
-- Name: ap_transdate_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ap_transdate_key ON ap USING btree (transdate);


--
-- Name: ap_vendor_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ap_vendor_id_key ON ap USING btree (vendor_id);


--
-- Name: ar_customer_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ar_customer_id_key ON ar USING btree (customer_id);


--
-- Name: ar_employee_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ar_employee_id_key ON ar USING btree (employee_id);


--
-- Name: ar_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ar_id_key ON ar USING btree (id);


--
-- Name: ar_invnumber_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ar_invnumber_key ON ar USING btree (lower(invnumber));


--
-- Name: ar_ordnumber_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ar_ordnumber_key ON ar USING btree (lower(ordnumber));


--
-- Name: ar_quonumber_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ar_quonumber_key ON ar USING btree (lower(quonumber));


--
-- Name: ar_transdate_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX ar_transdate_key ON ar USING btree (transdate);


--
-- Name: assembly_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX assembly_id_key ON assembly USING btree (id);


--
-- Name: chart_accno_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX chart_accno_key ON chart USING btree (accno);


--
-- Name: chart_category_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX chart_category_key ON chart USING btree (category);


--
-- Name: chart_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX chart_id_key ON chart USING btree (id);


--
-- Name: chart_link_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX chart_link_key ON chart USING btree (link);


--
-- Name: contacts_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX contacts_id_key ON contacts USING btree (cp_id);


--
-- Name: contacts_name_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX contacts_name_key ON contacts USING btree (cp_name);


--
-- Name: csv_import_report_rows_index_row; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX csv_import_report_rows_index_row ON csv_import_report_rows USING btree ("row");


--
-- Name: custom_variables_config_id_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX custom_variables_config_id_idx ON custom_variables USING btree (config_id);


--
-- Name: custom_variables_sub_module_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX custom_variables_sub_module_idx ON custom_variables USING btree (sub_module);


--
-- Name: custom_variables_trans_config_module_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX custom_variables_trans_config_module_idx ON custom_variables USING btree (config_id, trans_id, sub_module);


--
-- Name: customer_contact_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX customer_contact_key ON customer USING btree (contact);


--
-- Name: customer_customernumber_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX customer_customernumber_key ON customer USING btree (customernumber);


--
-- Name: customer_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX customer_id_key ON customer USING btree (id);


--
-- Name: customer_name_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX customer_name_key ON customer USING btree (name);


--
-- Name: department_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX department_id_key ON department USING btree (id);


--
-- Name: employee_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX employee_id_key ON employee USING btree (id);


--
-- Name: employee_login_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX employee_login_key ON employee USING btree (login);


--
-- Name: employee_name_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX employee_name_key ON employee USING btree (name);


--
-- Name: extrafld_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX extrafld_key ON extra_felder USING btree (owner);


--
-- Name: generic_translations_type_id_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX generic_translations_type_id_idx ON generic_translations USING btree (language_id, translation_type, translation_id);


--
-- Name: gl_description_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX gl_description_key ON gl USING btree (lower(description));


--
-- Name: gl_employee_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX gl_employee_id_key ON gl USING btree (employee_id);


--
-- Name: gl_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX gl_id_key ON gl USING btree (id);


--
-- Name: gl_reference_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX gl_reference_key ON gl USING btree (lower(reference));


--
-- Name: gl_transdate_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX gl_transdate_key ON gl USING btree (transdate);


--
-- Name: idx_custom_variables_validity_config_id_trans_id; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_custom_variables_validity_config_id_trans_id ON custom_variables_validity USING btree (config_id, trans_id);


--
-- Name: idx_custom_variables_validity_trans_id; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_custom_variables_validity_trans_id ON custom_variables_validity USING btree (trans_id);


--
-- Name: idx_record_links_from_id; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_record_links_from_id ON record_links USING btree (from_id);


--
-- Name: idx_record_links_from_table; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_record_links_from_table ON record_links USING btree (from_table);


--
-- Name: idx_record_links_to_id; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_record_links_to_id ON record_links USING btree (to_id);


--
-- Name: idx_record_links_to_table; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX idx_record_links_to_table ON record_links USING btree (to_table);


--
-- Name: invoice_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX invoice_id_key ON invoice USING btree (id);


--
-- Name: invoice_trans_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX invoice_trans_id_key ON invoice USING btree (trans_id);


--
-- Name: makemodel_model_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX makemodel_model_key ON makemodel USING btree (lower(model));


--
-- Name: makemodel_parts_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX makemodel_parts_id_key ON makemodel USING btree (parts_id);


--
-- Name: mid_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX mid_key ON contmasch USING btree (mid);


--
-- Name: oe_employee_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX oe_employee_id_key ON oe USING btree (employee_id);


--
-- Name: oe_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX oe_id_key ON oe USING btree (id);


--
-- Name: oe_ordnumber_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX oe_ordnumber_key ON oe USING btree (lower(ordnumber));


--
-- Name: oe_transdate_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX oe_transdate_key ON oe USING btree (transdate);


--
-- Name: orderitems_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX orderitems_id_key ON orderitems USING btree (id);


--
-- Name: orderitems_trans_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX orderitems_trans_id_key ON orderitems USING btree (trans_id);


--
-- Name: parts_description_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX parts_description_key ON parts USING btree (lower(description));


--
-- Name: parts_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX parts_id_key ON parts USING btree (id);


--
-- Name: parts_partnumber_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX parts_partnumber_key ON parts USING btree (lower(partnumber));


--
-- Name: project_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX project_id_key ON project USING btree (id);


--
-- Name: requirement_spec_items_item_type_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX requirement_spec_items_item_type_key ON requirement_spec_items USING btree (item_type);


--
-- Name: shipto_trans_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX shipto_trans_id_key ON shipto USING btree (trans_id);


--
-- Name: status_trans_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX status_trans_id_key ON status USING btree (trans_id);


--
-- Name: t_starttag_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX t_starttag_key ON termine USING btree (starttag);


--
-- Name: t_startzeit_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX t_startzeit_key ON termine USING btree (startzeit);


--
-- Name: t_stoptag_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX t_stoptag_key ON termine USING btree (stoptag);


--
-- Name: t_stopzeit_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX t_stopzeit_key ON termine USING btree (stopzeit);


--
-- Name: t_termin_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX t_termin_key ON termine USING btree (id);


--
-- Name: taxkeys_chartid_startdate; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE UNIQUE INDEX taxkeys_chartid_startdate ON taxkeys USING btree (chart_id, startdate);


--
-- Name: td_jahr_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX td_jahr_key ON termdate USING btree (jahr);


--
-- Name: td_monat_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX td_monat_key ON termdate USING btree (monat);


--
-- Name: td_tag_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX td_tag_key ON termdate USING btree (tag);


--
-- Name: td_termin_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX td_termin_key ON termdate USING btree (termid);


--
-- Name: telcall_bezug_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX telcall_bezug_key ON telcall USING btree (bezug);


--
-- Name: telcall_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX telcall_id_key ON telcall USING btree (id);


--
-- Name: tm_member_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX tm_member_key ON terminmember USING btree (member);


--
-- Name: tm_termin_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX tm_termin_key ON terminmember USING btree (termin);


--
-- Name: units_language_unit_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX units_language_unit_idx ON units_language USING btree (unit);


--
-- Name: units_name_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX units_name_idx ON units USING btree (name);


--
-- Name: vendor_contact_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX vendor_contact_key ON vendor USING btree (contact);


--
-- Name: vendor_id_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX vendor_id_key ON vendor USING btree (id);


--
-- Name: vendor_name_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX vendor_name_key ON vendor USING btree (name);


--
-- Name: vendor_vendornumber_key; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX vendor_vendornumber_key ON vendor USING btree (vendornumber);


--
-- Name: after_delete_ap_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER after_delete_ap_trigger AFTER DELETE ON ap FOR EACH ROW EXECUTE PROCEDURE clean_up_acc_trans_after_ar_ap_gl_delete();


--
-- Name: after_delete_ar_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER after_delete_ar_trigger AFTER DELETE ON ar FOR EACH ROW EXECUTE PROCEDURE clean_up_acc_trans_after_ar_ap_gl_delete();


--
-- Name: after_delete_customer_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER after_delete_customer_trigger AFTER DELETE ON customer FOR EACH ROW EXECUTE PROCEDURE clean_up_after_customer_vendor_delete();


--
-- Name: after_delete_delivery_term_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER after_delete_delivery_term_trigger AFTER DELETE ON delivery_terms FOR EACH ROW EXECUTE PROCEDURE generic_translations_delete_on_delivery_terms_delete_trigger();


--
-- Name: after_delete_gl_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER after_delete_gl_trigger AFTER DELETE ON gl FOR EACH ROW EXECUTE PROCEDURE clean_up_acc_trans_after_ar_ap_gl_delete();


--
-- Name: after_delete_payment_term_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER after_delete_payment_term_trigger AFTER DELETE ON payment_terms FOR EACH ROW EXECUTE PROCEDURE generic_translations_delete_on_payment_terms_delete_trigger();


--
-- Name: after_delete_requirement_spec_dependencies; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER after_delete_requirement_spec_dependencies AFTER DELETE ON requirement_specs FOR EACH ROW EXECUTE PROCEDURE requirement_spec_delete_trigger();


--
-- Name: after_delete_tax_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER after_delete_tax_trigger AFTER DELETE ON tax FOR EACH ROW EXECUTE PROCEDURE generic_translations_delete_on_tax_delete_trigger();


--
-- Name: after_delete_vendor_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER after_delete_vendor_trigger AFTER DELETE ON vendor FOR EACH ROW EXECUTE PROCEDURE clean_up_after_customer_vendor_delete();


--
-- Name: before_delete_ap_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER before_delete_ap_trigger BEFORE DELETE ON ap FOR EACH ROW EXECUTE PROCEDURE clean_up_record_links_before_ap_delete();


--
-- Name: before_delete_ar_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER before_delete_ar_trigger BEFORE DELETE ON ar FOR EACH ROW EXECUTE PROCEDURE clean_up_record_links_before_ar_delete();


--
-- Name: before_delete_delivery_order_items_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER before_delete_delivery_order_items_trigger BEFORE DELETE ON delivery_order_items FOR EACH ROW EXECUTE PROCEDURE clean_up_record_links_before_delivery_order_items_delete();


--
-- Name: before_delete_delivery_orders_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER before_delete_delivery_orders_trigger BEFORE DELETE ON delivery_orders FOR EACH ROW EXECUTE PROCEDURE clean_up_record_links_before_delivery_orders_delete();


--
-- Name: before_delete_invoice_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER before_delete_invoice_trigger BEFORE DELETE ON invoice FOR EACH ROW EXECUTE PROCEDURE clean_up_record_links_before_invoice_delete();


--
-- Name: before_delete_oe_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER before_delete_oe_trigger BEFORE DELETE ON oe FOR EACH ROW EXECUTE PROCEDURE clean_up_record_links_before_oe_delete();


--
-- Name: before_delete_orderitems_trigger; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER before_delete_orderitems_trigger BEFORE DELETE ON orderitems FOR EACH ROW EXECUTE PROCEDURE clean_up_record_links_before_orderitems_delete();


--
-- Name: check_bin_wh_delivery_order_items_stock; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER check_bin_wh_delivery_order_items_stock BEFORE INSERT OR UPDATE ON delivery_order_items_stock FOR EACH ROW EXECUTE PROCEDURE check_bin_belongs_to_wh();


--
-- Name: check_bin_wh_inventory; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER check_bin_wh_inventory BEFORE INSERT OR UPDATE ON inventory FOR EACH ROW EXECUTE PROCEDURE check_bin_belongs_to_wh();


--
-- Name: check_bin_wh_parts; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER check_bin_wh_parts BEFORE INSERT OR UPDATE ON parts FOR EACH ROW EXECUTE PROCEDURE check_bin_belongs_to_wh();


--
-- Name: check_inventory; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER check_inventory AFTER UPDATE ON oe FOR EACH ROW EXECUTE PROCEDURE check_inventory();


--
-- Name: contacts_delete_custom_variables_after_deletion; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER contacts_delete_custom_variables_after_deletion AFTER DELETE ON contacts FOR EACH ROW EXECUTE PROCEDURE delete_custom_variables_trigger();


--
-- Name: customer_before_delete_clear_follow_ups; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER customer_before_delete_clear_follow_ups AFTER DELETE ON customer FOR EACH ROW EXECUTE PROCEDURE follow_up_delete_when_customer_vendor_is_deleted_trigger();


--
-- Name: customer_delete_custom_variables_after_deletion; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER customer_delete_custom_variables_after_deletion AFTER DELETE ON customer FOR EACH ROW EXECUTE PROCEDURE delete_custom_variables_trigger();


--
-- Name: delete_delivery_orders_dependencies; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER delete_delivery_orders_dependencies BEFORE DELETE ON delivery_orders FOR EACH ROW EXECUTE PROCEDURE delivery_orders_before_delete_trigger();


--
-- Name: delete_oe_dependencies; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER delete_oe_dependencies BEFORE DELETE ON oe FOR EACH ROW EXECUTE PROCEDURE oe_before_delete_trigger();


--
-- Name: delete_requirement_spec_dependencies; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER delete_requirement_spec_dependencies BEFORE DELETE ON requirement_specs FOR EACH ROW EXECUTE PROCEDURE requirement_spec_delete_trigger();


--
-- Name: delete_requirement_spec_item_dependencies; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER delete_requirement_spec_item_dependencies BEFORE DELETE ON requirement_spec_items FOR EACH ROW EXECUTE PROCEDURE requirement_spec_item_before_delete_trigger();


--
-- Name: delivery_order_items_delete_custom_variables_after_deletion; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER delivery_order_items_delete_custom_variables_after_deletion AFTER DELETE ON delivery_order_items FOR EACH ROW EXECUTE PROCEDURE delete_custom_variables_trigger();


--
-- Name: delivery_orders_on_update_close_follow_up; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER delivery_orders_on_update_close_follow_up AFTER UPDATE ON delivery_orders FOR EACH ROW EXECUTE PROCEDURE follow_up_close_when_oe_closed_trigger();


--
-- Name: follow_up_delete_notes; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER follow_up_delete_notes AFTER DELETE ON follow_ups FOR EACH ROW EXECUTE PROCEDURE follow_up_delete_notes_trigger();


--
-- Name: invoice_delete_custom_variables_after_deletion; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER invoice_delete_custom_variables_after_deletion AFTER DELETE ON invoice FOR EACH ROW EXECUTE PROCEDURE delete_custom_variables_trigger();


--
-- Name: mtime_acc_trans; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_acc_trans BEFORE UPDATE ON acc_trans FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_ap; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_ap BEFORE UPDATE ON ap FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_ar; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_ar BEFORE UPDATE ON ar FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_bin; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_bin BEFORE UPDATE ON bin FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_chart; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_chart BEFORE UPDATE ON chart FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_contacts; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_contacts BEFORE UPDATE ON contacts FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_custom_variable_config_partsgroups; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_custom_variable_config_partsgroups BEFORE UPDATE ON custom_variable_config_partsgroups FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_custom_variable_configs; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_custom_variable_configs BEFORE UPDATE ON custom_variable_configs FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_custom_variables; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_custom_variables BEFORE UPDATE ON custom_variables FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_customer; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_customer BEFORE UPDATE ON customer FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_delivery_order_items_id; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_delivery_order_items_id BEFORE UPDATE ON delivery_order_items FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_delivery_order_items_stock; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_delivery_order_items_stock BEFORE UPDATE ON delivery_order_items_stock FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_delivery_orders; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_delivery_orders BEFORE UPDATE ON delivery_orders FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_delivery_terms; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_delivery_terms BEFORE UPDATE ON delivery_terms FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_department; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_department BEFORE UPDATE ON department FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_dunning; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_dunning BEFORE UPDATE ON dunning FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_follow_up_links; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_follow_up_links BEFORE UPDATE ON follow_up_links FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_follow_ups; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_follow_ups BEFORE UPDATE ON follow_ups FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_gl; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_gl BEFORE UPDATE ON gl FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_inventory; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_inventory BEFORE UPDATE ON inventory FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_invoice; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_invoice BEFORE UPDATE ON invoice FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_notes; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_notes BEFORE UPDATE ON notes FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_oe; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_oe BEFORE UPDATE ON oe FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_orderitems; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_orderitems BEFORE UPDATE ON orderitems FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_parts; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_parts BEFORE UPDATE ON parts FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_partsgroup; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_partsgroup BEFORE UPDATE ON partsgroup FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_price_rule_items; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_price_rule_items BEFORE UPDATE ON price_rule_items FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_price_rules; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_price_rules BEFORE UPDATE ON price_rules FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_project_participants; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_project_participants BEFORE UPDATE ON project_participants FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_project_phase_paticipants; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_project_phase_paticipants BEFORE UPDATE ON project_phase_participants FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_project_phases; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_project_phases BEFORE UPDATE ON project_phases FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_project_roles; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_project_roles BEFORE UPDATE ON project_roles FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_project_status; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_project_status BEFORE UPDATE ON project_statuses FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_spec_acceptance_statuses; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_spec_acceptance_statuses BEFORE UPDATE ON requirement_spec_acceptance_statuses FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_spec_complexities; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_spec_complexities BEFORE UPDATE ON requirement_spec_complexities FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_spec_items; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_spec_items BEFORE UPDATE ON requirement_spec_items FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_spec_orders; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_spec_orders BEFORE UPDATE ON requirement_spec_orders FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_spec_pictures; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_spec_pictures BEFORE UPDATE ON requirement_spec_pictures FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_spec_predefined_texts; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_spec_predefined_texts BEFORE UPDATE ON requirement_spec_predefined_texts FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_spec_risks; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_spec_risks BEFORE UPDATE ON requirement_spec_risks FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_spec_statuses; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_spec_statuses BEFORE UPDATE ON requirement_spec_statuses FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_spec_text_blocks; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_spec_text_blocks BEFORE UPDATE ON requirement_spec_text_blocks FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_spec_types; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_spec_types BEFORE UPDATE ON requirement_spec_types FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_spec_versions; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_spec_versions BEFORE UPDATE ON requirement_spec_versions FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_requirement_specs; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_requirement_specs BEFORE UPDATE ON requirement_specs FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_status; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_status BEFORE UPDATE ON status FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_tax; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_tax BEFORE UPDATE ON tax FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_transfer_type; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_transfer_type BEFORE UPDATE ON transfer_type FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_vendor; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_vendor BEFORE UPDATE ON vendor FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: mtime_warehouse; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER mtime_warehouse BEFORE UPDATE ON warehouse FOR EACH ROW EXECUTE PROCEDURE set_mtime();


--
-- Name: oe_before_delete_clear_follow_ups; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER oe_before_delete_clear_follow_ups BEFORE DELETE ON oe FOR EACH ROW EXECUTE PROCEDURE follow_up_delete_when_oe_is_deleted_trigger();


--
-- Name: oe_on_update_close_follow_up; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER oe_on_update_close_follow_up AFTER UPDATE ON oe FOR EACH ROW EXECUTE PROCEDURE follow_up_close_when_oe_closed_trigger();


--
-- Name: orderitems_delete_custom_variables_after_deletion; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER orderitems_delete_custom_variables_after_deletion AFTER DELETE ON orderitems FOR EACH ROW EXECUTE PROCEDURE delete_custom_variables_trigger();


--
-- Name: parts_delete_custom_variables_after_deletion; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER parts_delete_custom_variables_after_deletion AFTER DELETE ON parts FOR EACH ROW EXECUTE PROCEDURE delete_custom_variables_trigger();


--
-- Name: priceupdate_parts; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER priceupdate_parts AFTER UPDATE ON parts FOR EACH ROW EXECUTE PROCEDURE set_priceupdate_parts();


--
-- Name: project_delete_custom_variables_after_deletion; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER project_delete_custom_variables_after_deletion AFTER DELETE ON project FOR EACH ROW EXECUTE PROCEDURE delete_custom_variables_trigger();


--
-- Name: trig_assembly_purchase_price; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trig_assembly_purchase_price AFTER INSERT OR DELETE OR UPDATE ON assembly FOR EACH ROW EXECUTE PROCEDURE update_purchase_price();


--
-- Name: trig_update_onhand; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER trig_update_onhand AFTER INSERT OR DELETE OR UPDATE ON inventory FOR EACH ROW EXECUTE PROCEDURE update_onhand();


--
-- Name: update_requirement_spec_item_time_estimation; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER update_requirement_spec_item_time_estimation AFTER INSERT OR DELETE OR UPDATE ON requirement_spec_items FOR EACH ROW EXECUTE PROCEDURE requirement_spec_item_time_estimation_updater_trigger();


--
-- Name: vendor_before_delete_clear_follow_ups; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER vendor_before_delete_clear_follow_ups AFTER DELETE ON vendor FOR EACH ROW EXECUTE PROCEDURE follow_up_delete_when_customer_vendor_is_deleted_trigger();


--
-- Name: vendor_delete_custom_variables_after_deletion; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER vendor_delete_custom_variables_after_deletion AFTER DELETE ON vendor FOR EACH ROW EXECUTE PROCEDURE delete_custom_variables_trigger();


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY invoice
    ADD CONSTRAINT "$1" FOREIGN KEY (parts_id) REFERENCES parts(id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT "$1" FOREIGN KEY (customer_id) REFERENCES customer(id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT "$1" FOREIGN KEY (vendor_id) REFERENCES vendor(id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY prices
    ADD CONSTRAINT "$1" FOREIGN KEY (parts_id) REFERENCES parts(id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY units
    ADD CONSTRAINT "$1" FOREIGN KEY (base_unit) REFERENCES units(name);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY parts
    ADD CONSTRAINT "$1" FOREIGN KEY (buchungsgruppen_id) REFERENCES buchungsgruppen(id);


--
-- Name: $1; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY acc_trans
    ADD CONSTRAINT "$1" FOREIGN KEY (chart_id) REFERENCES chart(id);


--
-- Name: $2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY prices
    ADD CONSTRAINT "$2" FOREIGN KEY (pricegroup_id) REFERENCES pricegroup(id);


--
-- Name: acc_trans_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY acc_trans
    ADD CONSTRAINT acc_trans_project_id_fkey FOREIGN KEY (project_id) REFERENCES project(id);


--
-- Name: acc_trans_tax_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY acc_trans
    ADD CONSTRAINT acc_trans_tax_id_fkey FOREIGN KEY (tax_id) REFERENCES tax(id);


--
-- Name: ap_cp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT ap_cp_id_fkey FOREIGN KEY (cp_id) REFERENCES contacts(cp_id);


--
-- Name: ap_currency_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT ap_currency_id_fkey FOREIGN KEY (currency_id) REFERENCES currencies(id);


--
-- Name: ap_delivery_term_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT ap_delivery_term_id_fkey FOREIGN KEY (delivery_term_id) REFERENCES delivery_terms(id);


--
-- Name: ap_department_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT ap_department_id_fkey FOREIGN KEY (department_id) REFERENCES department(id);


--
-- Name: ap_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT ap_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: ap_globalproject_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT ap_globalproject_id_fkey FOREIGN KEY (globalproject_id) REFERENCES project(id);


--
-- Name: ap_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT ap_language_id_fkey FOREIGN KEY (language_id) REFERENCES language(id);


--
-- Name: ap_payment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT ap_payment_id_fkey FOREIGN KEY (payment_id) REFERENCES payment_terms(id);


--
-- Name: ap_storno_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT ap_storno_id_fkey FOREIGN KEY (storno_id) REFERENCES ap(id);


--
-- Name: ap_taxzone_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ap
    ADD CONSTRAINT ap_taxzone_id_fkey FOREIGN KEY (taxzone_id) REFERENCES tax_zones(id);


--
-- Name: ar_cp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_cp_id_fkey FOREIGN KEY (cp_id) REFERENCES contacts(cp_id);


--
-- Name: ar_currency_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_currency_id_fkey FOREIGN KEY (currency_id) REFERENCES currencies(id);


--
-- Name: ar_delivery_term_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_delivery_term_id_fkey FOREIGN KEY (delivery_term_id) REFERENCES delivery_terms(id);


--
-- Name: ar_department_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_department_id_fkey FOREIGN KEY (department_id) REFERENCES department(id);


--
-- Name: ar_dunning_config_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_dunning_config_id_fkey FOREIGN KEY (dunning_config_id) REFERENCES dunning_config(id);


--
-- Name: ar_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: ar_globalproject_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_globalproject_id_fkey FOREIGN KEY (globalproject_id) REFERENCES project(id);


--
-- Name: ar_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_language_id_fkey FOREIGN KEY (language_id) REFERENCES language(id);


--
-- Name: ar_payment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_payment_id_fkey FOREIGN KEY (payment_id) REFERENCES payment_terms(id);


--
-- Name: ar_salesman_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_salesman_id_fkey FOREIGN KEY (salesman_id) REFERENCES employee(id);


--
-- Name: ar_shipto_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_shipto_id_fkey FOREIGN KEY (shipto_id) REFERENCES shipto(shipto_id);


--
-- Name: ar_storno_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_storno_id_fkey FOREIGN KEY (storno_id) REFERENCES ar(id);


--
-- Name: ar_taxzone_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY ar
    ADD CONSTRAINT ar_taxzone_id_fkey FOREIGN KEY (taxzone_id) REFERENCES tax_zones(id);


--
-- Name: bank_accounts_chart_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bank_accounts
    ADD CONSTRAINT bank_accounts_chart_id_fkey FOREIGN KEY (chart_id) REFERENCES chart(id);


--
-- Name: bin_warehouse_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY bin
    ADD CONSTRAINT bin_warehouse_id_fkey FOREIGN KEY (warehouse_id) REFERENCES warehouse(id);


--
-- Name: csv_import_profile_settings_csv_import_profile_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY csv_import_profile_settings
    ADD CONSTRAINT csv_import_profile_settings_csv_import_profile_id_fkey FOREIGN KEY (csv_import_profile_id) REFERENCES csv_import_profiles(id);


--
-- Name: csv_import_report_rows_csv_import_report_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY csv_import_report_rows
    ADD CONSTRAINT csv_import_report_rows_csv_import_report_id_fkey FOREIGN KEY (csv_import_report_id) REFERENCES csv_import_reports(id);


--
-- Name: csv_import_report_status_csv_import_report_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY csv_import_report_status
    ADD CONSTRAINT csv_import_report_status_csv_import_report_id_fkey FOREIGN KEY (csv_import_report_id) REFERENCES csv_import_reports(id);


--
-- Name: csv_import_reports_profile_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY csv_import_reports
    ADD CONSTRAINT csv_import_reports_profile_id_fkey FOREIGN KEY (profile_id) REFERENCES csv_import_profiles(id);


--
-- Name: custom_variable_config_partsgrou_custom_variable_config_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY custom_variable_config_partsgroups
    ADD CONSTRAINT custom_variable_config_partsgrou_custom_variable_config_id_fkey FOREIGN KEY (custom_variable_config_id) REFERENCES custom_variable_configs(id);


--
-- Name: custom_variable_config_partsgroups_partsgroup_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY custom_variable_config_partsgroups
    ADD CONSTRAINT custom_variable_config_partsgroups_partsgroup_id_fkey FOREIGN KEY (partsgroup_id) REFERENCES partsgroup(id);


--
-- Name: custom_variables_config_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY custom_variables
    ADD CONSTRAINT custom_variables_config_id_fkey FOREIGN KEY (config_id) REFERENCES custom_variable_configs(id);


--
-- Name: custom_variables_validity_config_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY custom_variables_validity
    ADD CONSTRAINT custom_variables_validity_config_id_fkey FOREIGN KEY (config_id) REFERENCES custom_variable_configs(id);


--
-- Name: customer_business_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY customer
    ADD CONSTRAINT customer_business_id_fkey FOREIGN KEY (business_id) REFERENCES business(id);


--
-- Name: customer_currency_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY customer
    ADD CONSTRAINT customer_currency_id_fkey FOREIGN KEY (currency_id) REFERENCES currencies(id);


--
-- Name: customer_delivery_term_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY customer
    ADD CONSTRAINT customer_delivery_term_id_fkey FOREIGN KEY (delivery_term_id) REFERENCES delivery_terms(id);


--
-- Name: customer_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY customer
    ADD CONSTRAINT customer_language_id_fkey FOREIGN KEY (language_id) REFERENCES language(id);


--
-- Name: customer_payment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY customer
    ADD CONSTRAINT customer_payment_id_fkey FOREIGN KEY (payment_id) REFERENCES payment_terms(id);


--
-- Name: customer_taxzone_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY customer
    ADD CONSTRAINT customer_taxzone_id_fkey FOREIGN KEY (taxzone_id) REFERENCES tax_zones(id);


--
-- Name: defaults_bin_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY defaults
    ADD CONSTRAINT defaults_bin_id_fkey FOREIGN KEY (bin_id) REFERENCES bin(id);


--
-- Name: defaults_bin_id_ignore_onhand_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY defaults
    ADD CONSTRAINT defaults_bin_id_ignore_onhand_fkey FOREIGN KEY (bin_id_ignore_onhand) REFERENCES bin(id);


--
-- Name: defaults_currency_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY defaults
    ADD CONSTRAINT defaults_currency_id_fkey FOREIGN KEY (currency_id) REFERENCES currencies(id);


--
-- Name: defaults_requirement_spec_section_order_part_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY defaults
    ADD CONSTRAINT defaults_requirement_spec_section_order_part_id_fkey FOREIGN KEY (requirement_spec_section_order_part_id) REFERENCES parts(id) ON DELETE SET NULL;


--
-- Name: defaults_warehouse_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY defaults
    ADD CONSTRAINT defaults_warehouse_id_fkey FOREIGN KEY (warehouse_id) REFERENCES warehouse(id);


--
-- Name: defaults_warehouse_id_ignore_onhand_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY defaults
    ADD CONSTRAINT defaults_warehouse_id_ignore_onhand_fkey FOREIGN KEY (warehouse_id_ignore_onhand) REFERENCES warehouse(id);


--
-- Name: delivery_order_items_delivery_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_order_items
    ADD CONSTRAINT delivery_order_items_delivery_order_id_fkey FOREIGN KEY (delivery_order_id) REFERENCES delivery_orders(id) ON DELETE CASCADE;


--
-- Name: delivery_order_items_parts_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_order_items
    ADD CONSTRAINT delivery_order_items_parts_id_fkey FOREIGN KEY (parts_id) REFERENCES parts(id) ON DELETE RESTRICT;


--
-- Name: delivery_order_items_price_factor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_order_items
    ADD CONSTRAINT delivery_order_items_price_factor_id_fkey FOREIGN KEY (price_factor_id) REFERENCES price_factors(id) ON DELETE RESTRICT;


--
-- Name: delivery_order_items_pricegroup_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_order_items
    ADD CONSTRAINT delivery_order_items_pricegroup_id_fkey FOREIGN KEY (pricegroup_id) REFERENCES pricegroup(id) ON DELETE RESTRICT;


--
-- Name: delivery_order_items_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_order_items
    ADD CONSTRAINT delivery_order_items_project_id_fkey FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE SET NULL;


--
-- Name: delivery_order_items_stock_bin_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_order_items_stock
    ADD CONSTRAINT delivery_order_items_stock_bin_id_fkey FOREIGN KEY (bin_id) REFERENCES bin(id) ON DELETE RESTRICT;


--
-- Name: delivery_order_items_stock_delivery_order_item_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_order_items_stock
    ADD CONSTRAINT delivery_order_items_stock_delivery_order_item_id_fkey FOREIGN KEY (delivery_order_item_id) REFERENCES delivery_order_items(id) ON DELETE CASCADE;


--
-- Name: delivery_order_items_stock_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT delivery_order_items_stock_id_fkey FOREIGN KEY (delivery_order_items_stock_id) REFERENCES delivery_order_items_stock(id);


--
-- Name: delivery_order_items_stock_warehouse_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_order_items_stock
    ADD CONSTRAINT delivery_order_items_stock_warehouse_id_fkey FOREIGN KEY (warehouse_id) REFERENCES warehouse(id) ON DELETE RESTRICT;


--
-- Name: delivery_order_items_unit_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_order_items
    ADD CONSTRAINT delivery_order_items_unit_fkey FOREIGN KEY (unit) REFERENCES units(name);


--
-- Name: delivery_orders_cp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_cp_id_fkey FOREIGN KEY (cp_id) REFERENCES contacts(cp_id);


--
-- Name: delivery_orders_currency_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_currency_id_fkey FOREIGN KEY (currency_id) REFERENCES currencies(id);


--
-- Name: delivery_orders_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES customer(id);


--
-- Name: delivery_orders_delivery_term_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_delivery_term_id_fkey FOREIGN KEY (delivery_term_id) REFERENCES delivery_terms(id);


--
-- Name: delivery_orders_department_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_department_id_fkey FOREIGN KEY (department_id) REFERENCES department(id);


--
-- Name: delivery_orders_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: delivery_orders_globalproject_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_globalproject_id_fkey FOREIGN KEY (globalproject_id) REFERENCES project(id);


--
-- Name: delivery_orders_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_language_id_fkey FOREIGN KEY (language_id) REFERENCES language(id);


--
-- Name: delivery_orders_salesman_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_salesman_id_fkey FOREIGN KEY (salesman_id) REFERENCES employee(id);


--
-- Name: delivery_orders_shipto_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_shipto_id_fkey FOREIGN KEY (shipto_id) REFERENCES shipto(shipto_id);


--
-- Name: delivery_orders_taxzone_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_taxzone_id_fkey FOREIGN KEY (taxzone_id) REFERENCES tax_zones(id);


--
-- Name: delivery_orders_vendor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY delivery_orders
    ADD CONSTRAINT delivery_orders_vendor_id_fkey FOREIGN KEY (vendor_id) REFERENCES vendor(id);


--
-- Name: drafts_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY drafts
    ADD CONSTRAINT drafts_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: dunning_dunning_config_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY dunning
    ADD CONSTRAINT dunning_dunning_config_id_fkey FOREIGN KEY (dunning_config_id) REFERENCES dunning_config(id);


--
-- Name: dunning_fee_interest_ar_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY dunning
    ADD CONSTRAINT dunning_fee_interest_ar_id_fkey FOREIGN KEY (fee_interest_ar_id) REFERENCES ar(id);


--
-- Name: exchangerate_currency_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY exchangerate
    ADD CONSTRAINT exchangerate_currency_id_fkey FOREIGN KEY (currency_id) REFERENCES currencies(id);


--
-- Name: follow_up_access_what_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY follow_up_access
    ADD CONSTRAINT follow_up_access_what_fkey FOREIGN KEY (what) REFERENCES employee(id);


--
-- Name: follow_up_access_who_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY follow_up_access
    ADD CONSTRAINT follow_up_access_who_fkey FOREIGN KEY (who) REFERENCES employee(id);


--
-- Name: follow_up_links_follow_up_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY follow_up_links
    ADD CONSTRAINT follow_up_links_follow_up_id_fkey FOREIGN KEY (follow_up_id) REFERENCES follow_ups(id) ON DELETE CASCADE;


--
-- Name: follow_ups_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY follow_ups
    ADD CONSTRAINT follow_ups_created_by_fkey FOREIGN KEY (created_by) REFERENCES employee(id);


--
-- Name: follow_ups_created_for_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY follow_ups
    ADD CONSTRAINT follow_ups_created_for_user_fkey FOREIGN KEY (created_for_user) REFERENCES employee(id);


--
-- Name: follow_ups_note_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY follow_ups
    ADD CONSTRAINT follow_ups_note_id_fkey FOREIGN KEY (note_id) REFERENCES notes(id);


--
-- Name: generic_translations_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY generic_translations
    ADD CONSTRAINT generic_translations_language_id_fkey FOREIGN KEY (language_id) REFERENCES language(id);


--
-- Name: gl_department_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gl
    ADD CONSTRAINT gl_department_id_fkey FOREIGN KEY (department_id) REFERENCES department(id);


--
-- Name: gl_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gl
    ADD CONSTRAINT gl_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: gl_storno_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY gl
    ADD CONSTRAINT gl_storno_id_fkey FOREIGN KEY (storno_id) REFERENCES gl(id);


--
-- Name: history_erp_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY history_erp
    ADD CONSTRAINT history_erp_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: inventory_bin_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT inventory_bin_id_fkey FOREIGN KEY (bin_id) REFERENCES bin(id);


--
-- Name: inventory_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT inventory_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: inventory_parts_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT inventory_parts_id_fkey FOREIGN KEY (parts_id) REFERENCES parts(id);


--
-- Name: inventory_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT inventory_project_id_fkey FOREIGN KEY (project_id) REFERENCES project(id);


--
-- Name: inventory_trans_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT inventory_trans_type_id_fkey FOREIGN KEY (trans_type_id) REFERENCES transfer_type(id);


--
-- Name: inventory_warehouse_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT inventory_warehouse_id_fkey FOREIGN KEY (warehouse_id) REFERENCES warehouse(id);


--
-- Name: invoice_price_factor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY invoice
    ADD CONSTRAINT invoice_price_factor_id_fkey FOREIGN KEY (price_factor_id) REFERENCES price_factors(id);


--
-- Name: invoice_pricegroup_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY invoice
    ADD CONSTRAINT invoice_pricegroup_id_fkey FOREIGN KEY (pricegroup_id) REFERENCES pricegroup(id);


--
-- Name: invoice_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY invoice
    ADD CONSTRAINT invoice_project_id_fkey FOREIGN KEY (project_id) REFERENCES project(id);


--
-- Name: invoice_unit_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY invoice
    ADD CONSTRAINT invoice_unit_fkey FOREIGN KEY (unit) REFERENCES units(name);


--
-- Name: notes_created_by_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY notes
    ADD CONSTRAINT notes_created_by_fkey FOREIGN KEY (created_by) REFERENCES employee(id);


--
-- Name: oe_cp_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_cp_id_fkey FOREIGN KEY (cp_id) REFERENCES contacts(cp_id);


--
-- Name: oe_currency_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_currency_id_fkey FOREIGN KEY (currency_id) REFERENCES currencies(id);


--
-- Name: oe_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES customer(id);


--
-- Name: oe_delivery_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_delivery_customer_id_fkey FOREIGN KEY (delivery_customer_id) REFERENCES customer(id);


--
-- Name: oe_delivery_term_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_delivery_term_id_fkey FOREIGN KEY (delivery_term_id) REFERENCES delivery_terms(id);


--
-- Name: oe_delivery_vendor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_delivery_vendor_id_fkey FOREIGN KEY (delivery_vendor_id) REFERENCES vendor(id);


--
-- Name: oe_department_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_department_id_fkey FOREIGN KEY (department_id) REFERENCES department(id);


--
-- Name: oe_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: oe_globalproject_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_globalproject_id_fkey FOREIGN KEY (globalproject_id) REFERENCES project(id);


--
-- Name: oe_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY inventory
    ADD CONSTRAINT oe_id_fkey FOREIGN KEY (oe_id) REFERENCES delivery_orders(id);


--
-- Name: oe_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_language_id_fkey FOREIGN KEY (language_id) REFERENCES language(id);


--
-- Name: oe_payment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_payment_id_fkey FOREIGN KEY (payment_id) REFERENCES payment_terms(id);


--
-- Name: oe_salesman_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_salesman_id_fkey FOREIGN KEY (salesman_id) REFERENCES employee(id);


--
-- Name: oe_shipto_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_shipto_id_fkey FOREIGN KEY (shipto_id) REFERENCES shipto(shipto_id);


--
-- Name: oe_taxzone_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_taxzone_id_fkey FOREIGN KEY (taxzone_id) REFERENCES tax_zones(id);


--
-- Name: oe_vendor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY oe
    ADD CONSTRAINT oe_vendor_id_fkey FOREIGN KEY (vendor_id) REFERENCES vendor(id);


--
-- Name: orderitems_parts_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT orderitems_parts_id_fkey FOREIGN KEY (parts_id) REFERENCES parts(id) ON DELETE RESTRICT;


--
-- Name: orderitems_price_factor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT orderitems_price_factor_id_fkey FOREIGN KEY (price_factor_id) REFERENCES price_factors(id) ON DELETE RESTRICT;


--
-- Name: orderitems_pricegroup_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT orderitems_pricegroup_id_fkey FOREIGN KEY (pricegroup_id) REFERENCES pricegroup(id) ON DELETE RESTRICT;


--
-- Name: orderitems_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT orderitems_project_id_fkey FOREIGN KEY (project_id) REFERENCES project(id) ON DELETE SET NULL;


--
-- Name: orderitems_trans_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT orderitems_trans_id_fkey FOREIGN KEY (trans_id) REFERENCES oe(id) ON DELETE CASCADE;


--
-- Name: orderitems_unit_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY orderitems
    ADD CONSTRAINT orderitems_unit_fkey FOREIGN KEY (unit) REFERENCES units(name);


--
-- Name: parts_bin_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY parts
    ADD CONSTRAINT parts_bin_id_fkey FOREIGN KEY (bin_id) REFERENCES bin(id);


--
-- Name: parts_partsgroup_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY parts
    ADD CONSTRAINT parts_partsgroup_id_fkey FOREIGN KEY (partsgroup_id) REFERENCES partsgroup(id);


--
-- Name: parts_payment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY parts
    ADD CONSTRAINT parts_payment_id_fkey FOREIGN KEY (payment_id) REFERENCES payment_terms(id);


--
-- Name: parts_price_factor_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY parts
    ADD CONSTRAINT parts_price_factor_id_fkey FOREIGN KEY (price_factor_id) REFERENCES price_factors(id);


--
-- Name: parts_unit_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY parts
    ADD CONSTRAINT parts_unit_fkey FOREIGN KEY (unit) REFERENCES units(name);


--
-- Name: parts_warehouse_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY parts
    ADD CONSTRAINT parts_warehouse_id_fkey FOREIGN KEY (warehouse_id) REFERENCES warehouse(id);


--
-- Name: periodic_invoices_ar_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY periodic_invoices
    ADD CONSTRAINT periodic_invoices_ar_id_fkey FOREIGN KEY (ar_id) REFERENCES ar(id) ON DELETE CASCADE;


--
-- Name: periodic_invoices_config_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY periodic_invoices
    ADD CONSTRAINT periodic_invoices_config_id_fkey FOREIGN KEY (config_id) REFERENCES periodic_invoices_configs(id) ON DELETE CASCADE;


--
-- Name: periodic_invoices_configs_ar_chart_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY periodic_invoices_configs
    ADD CONSTRAINT periodic_invoices_configs_ar_chart_id_fkey FOREIGN KEY (ar_chart_id) REFERENCES chart(id) ON DELETE RESTRICT;


--
-- Name: periodic_invoices_configs_oe_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY periodic_invoices_configs
    ADD CONSTRAINT periodic_invoices_configs_oe_id_fkey FOREIGN KEY (oe_id) REFERENCES oe(id) ON DELETE CASCADE;


--
-- Name: periodic_invoices_configs_printer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY periodic_invoices_configs
    ADD CONSTRAINT periodic_invoices_configs_printer_id_fkey FOREIGN KEY (printer_id) REFERENCES printers(id) ON DELETE SET NULL;


--
-- Name: price_rule_items_custom_variable_configs_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY price_rule_items
    ADD CONSTRAINT price_rule_items_custom_variable_configs_id_fkey FOREIGN KEY (custom_variable_configs_id) REFERENCES custom_variable_configs(id);


--
-- Name: price_rule_items_price_rules_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY price_rule_items
    ADD CONSTRAINT price_rule_items_price_rules_id_fkey FOREIGN KEY (price_rules_id) REFERENCES price_rules(id) ON DELETE CASCADE;


--
-- Name: project_billable_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project
    ADD CONSTRAINT project_billable_customer_id_fkey FOREIGN KEY (billable_customer_id) REFERENCES customer(id);


--
-- Name: project_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project
    ADD CONSTRAINT project_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES customer(id);


--
-- Name: project_participants_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_participants
    ADD CONSTRAINT project_participants_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: project_participants_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_participants
    ADD CONSTRAINT project_participants_project_id_fkey FOREIGN KEY (project_id) REFERENCES project(id);


--
-- Name: project_participants_project_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_participants
    ADD CONSTRAINT project_participants_project_role_id_fkey FOREIGN KEY (project_role_id) REFERENCES project_roles(id);


--
-- Name: project_phase_participants_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_phase_participants
    ADD CONSTRAINT project_phase_participants_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: project_phase_participants_project_phase_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_phase_participants
    ADD CONSTRAINT project_phase_participants_project_phase_id_fkey FOREIGN KEY (project_phase_id) REFERENCES project_phases(id);


--
-- Name: project_phase_participants_project_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_phase_participants
    ADD CONSTRAINT project_phase_participants_project_role_id_fkey FOREIGN KEY (project_role_id) REFERENCES project_roles(id);


--
-- Name: project_phases_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project_phases
    ADD CONSTRAINT project_phases_project_id_fkey FOREIGN KEY (project_id) REFERENCES project(id);


--
-- Name: project_project_status_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project
    ADD CONSTRAINT project_project_status_id_fkey FOREIGN KEY (project_status_id) REFERENCES project_statuses(id);


--
-- Name: project_project_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY project
    ADD CONSTRAINT project_project_type_id_fkey FOREIGN KEY (project_type_id) REFERENCES project_types(id);


--
-- Name: requirement_spec_item_dependencies_depended_item_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_item_dependencies
    ADD CONSTRAINT requirement_spec_item_dependencies_depended_item_id_fkey FOREIGN KEY (depended_item_id) REFERENCES requirement_spec_items(id) ON DELETE CASCADE;


--
-- Name: requirement_spec_item_dependencies_depending_item_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_item_dependencies
    ADD CONSTRAINT requirement_spec_item_dependencies_depending_item_id_fkey FOREIGN KEY (depending_item_id) REFERENCES requirement_spec_items(id) ON DELETE CASCADE;


--
-- Name: requirement_spec_items_acceptance_status_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_items
    ADD CONSTRAINT requirement_spec_items_acceptance_status_id_fkey FOREIGN KEY (acceptance_status_id) REFERENCES requirement_spec_acceptance_statuses(id);


--
-- Name: requirement_spec_items_complexity_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_items
    ADD CONSTRAINT requirement_spec_items_complexity_id_fkey FOREIGN KEY (complexity_id) REFERENCES requirement_spec_complexities(id);


--
-- Name: requirement_spec_items_order_part_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_items
    ADD CONSTRAINT requirement_spec_items_order_part_id_fkey FOREIGN KEY (order_part_id) REFERENCES parts(id) ON DELETE SET NULL;


--
-- Name: requirement_spec_items_parent_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_items
    ADD CONSTRAINT requirement_spec_items_parent_id_fkey FOREIGN KEY (parent_id) REFERENCES requirement_spec_items(id);


--
-- Name: requirement_spec_items_requirement_spec_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_items
    ADD CONSTRAINT requirement_spec_items_requirement_spec_id_fkey FOREIGN KEY (requirement_spec_id) REFERENCES requirement_specs(id) ON DELETE CASCADE;


--
-- Name: requirement_spec_items_risk_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_items
    ADD CONSTRAINT requirement_spec_items_risk_id_fkey FOREIGN KEY (risk_id) REFERENCES requirement_spec_risks(id);


--
-- Name: requirement_spec_orders_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_orders
    ADD CONSTRAINT requirement_spec_orders_order_id_fkey FOREIGN KEY (order_id) REFERENCES oe(id) ON DELETE CASCADE;


--
-- Name: requirement_spec_orders_requirement_spec_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_orders
    ADD CONSTRAINT requirement_spec_orders_requirement_spec_id_fkey FOREIGN KEY (requirement_spec_id) REFERENCES requirement_specs(id) ON DELETE CASCADE;


--
-- Name: requirement_spec_orders_version_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_orders
    ADD CONSTRAINT requirement_spec_orders_version_id_fkey FOREIGN KEY (version_id) REFERENCES requirement_spec_versions(id) ON DELETE SET NULL;


--
-- Name: requirement_spec_parts_part_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_parts
    ADD CONSTRAINT requirement_spec_parts_part_id_fkey FOREIGN KEY (part_id) REFERENCES parts(id);


--
-- Name: requirement_spec_parts_requirement_spec_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_parts
    ADD CONSTRAINT requirement_spec_parts_requirement_spec_id_fkey FOREIGN KEY (requirement_spec_id) REFERENCES requirement_specs(id);


--
-- Name: requirement_spec_parts_unit_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_parts
    ADD CONSTRAINT requirement_spec_parts_unit_id_fkey FOREIGN KEY (unit_id) REFERENCES units(id);


--
-- Name: requirement_spec_pictures_requirement_spec_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_pictures
    ADD CONSTRAINT requirement_spec_pictures_requirement_spec_id_fkey FOREIGN KEY (requirement_spec_id) REFERENCES requirement_specs(id) ON DELETE CASCADE;


--
-- Name: requirement_spec_pictures_text_block_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_pictures
    ADD CONSTRAINT requirement_spec_pictures_text_block_id_fkey FOREIGN KEY (text_block_id) REFERENCES requirement_spec_text_blocks(id) ON DELETE CASCADE;


--
-- Name: requirement_spec_text_blocks_requirement_spec_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_text_blocks
    ADD CONSTRAINT requirement_spec_text_blocks_requirement_spec_id_fkey FOREIGN KEY (requirement_spec_id) REFERENCES requirement_specs(id) ON DELETE CASCADE;


--
-- Name: requirement_spec_versions_requirement_spec_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_versions
    ADD CONSTRAINT requirement_spec_versions_requirement_spec_id_fkey FOREIGN KEY (requirement_spec_id) REFERENCES requirement_specs(id) ON DELETE CASCADE;


--
-- Name: requirement_spec_versions_working_copy_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_spec_versions
    ADD CONSTRAINT requirement_spec_versions_working_copy_id_fkey FOREIGN KEY (working_copy_id) REFERENCES requirement_specs(id) ON DELETE CASCADE;


--
-- Name: requirement_specs_customer_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_specs
    ADD CONSTRAINT requirement_specs_customer_id_fkey FOREIGN KEY (customer_id) REFERENCES customer(id);


--
-- Name: requirement_specs_project_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_specs
    ADD CONSTRAINT requirement_specs_project_id_fkey FOREIGN KEY (project_id) REFERENCES project(id);


--
-- Name: requirement_specs_status_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_specs
    ADD CONSTRAINT requirement_specs_status_id_fkey FOREIGN KEY (status_id) REFERENCES requirement_spec_statuses(id);


--
-- Name: requirement_specs_type_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_specs
    ADD CONSTRAINT requirement_specs_type_id_fkey FOREIGN KEY (type_id) REFERENCES requirement_spec_types(id);


--
-- Name: requirement_specs_working_copy_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY requirement_specs
    ADD CONSTRAINT requirement_specs_working_copy_id_fkey FOREIGN KEY (working_copy_id) REFERENCES requirement_specs(id) ON DELETE CASCADE;


--
-- Name: sepa_export_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY sepa_export
    ADD CONSTRAINT sepa_export_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: sepa_export_items_ap_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY sepa_export_items
    ADD CONSTRAINT sepa_export_items_ap_id_fkey FOREIGN KEY (ap_id) REFERENCES ap(id);


--
-- Name: sepa_export_items_ar_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY sepa_export_items
    ADD CONSTRAINT sepa_export_items_ar_id_fkey FOREIGN KEY (ar_id) REFERENCES ar(id);


--
-- Name: sepa_export_items_chart_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY sepa_export_items
    ADD CONSTRAINT sepa_export_items_chart_id_fkey FOREIGN KEY (chart_id) REFERENCES chart(id);


--
-- Name: sepa_export_items_sepa_export_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY sepa_export_items
    ADD CONSTRAINT sepa_export_items_sepa_export_id_fkey FOREIGN KEY (sepa_export_id) REFERENCES sepa_export(id);


--
-- Name: tax_chart_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY tax
    ADD CONSTRAINT tax_chart_id_fkey FOREIGN KEY (chart_id) REFERENCES chart(id);


--
-- Name: taxkeys_chart_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY taxkeys
    ADD CONSTRAINT taxkeys_chart_id_fkey FOREIGN KEY (chart_id) REFERENCES chart(id);


--
-- Name: taxkeys_tax_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY taxkeys
    ADD CONSTRAINT taxkeys_tax_id_fkey FOREIGN KEY (tax_id) REFERENCES tax(id);


--
-- Name: taxzone_charts_buchungsgruppen_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY taxzone_charts
    ADD CONSTRAINT taxzone_charts_buchungsgruppen_id_fkey FOREIGN KEY (buchungsgruppen_id) REFERENCES buchungsgruppen(id);


--
-- Name: taxzone_charts_expense_accno_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY taxzone_charts
    ADD CONSTRAINT taxzone_charts_expense_accno_id_fkey FOREIGN KEY (expense_accno_id) REFERENCES chart(id);


--
-- Name: taxzone_charts_income_accno_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY taxzone_charts
    ADD CONSTRAINT taxzone_charts_income_accno_id_fkey FOREIGN KEY (income_accno_id) REFERENCES chart(id);


--
-- Name: taxzone_charts_taxzone_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY taxzone_charts
    ADD CONSTRAINT taxzone_charts_taxzone_id_fkey FOREIGN KEY (taxzone_id) REFERENCES tax_zones(id);


--
-- Name: todo_user_config_employee_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY todo_user_config
    ADD CONSTRAINT todo_user_config_employee_id_fkey FOREIGN KEY (employee_id) REFERENCES employee(id);


--
-- Name: translation_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY translation
    ADD CONSTRAINT translation_language_id_fkey FOREIGN KEY (language_id) REFERENCES language(id);


--
-- Name: units_language_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY units_language
    ADD CONSTRAINT units_language_language_id_fkey FOREIGN KEY (language_id) REFERENCES language(id);


--
-- Name: units_language_unit_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY units_language
    ADD CONSTRAINT units_language_unit_fkey FOREIGN KEY (unit) REFERENCES units(name);


--
-- Name: vendor_business_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY vendor
    ADD CONSTRAINT vendor_business_id_fkey FOREIGN KEY (business_id) REFERENCES business(id);


--
-- Name: vendor_currency_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY vendor
    ADD CONSTRAINT vendor_currency_id_fkey FOREIGN KEY (currency_id) REFERENCES currencies(id);


--
-- Name: vendor_delivery_term_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY vendor
    ADD CONSTRAINT vendor_delivery_term_id_fkey FOREIGN KEY (delivery_term_id) REFERENCES delivery_terms(id);


--
-- Name: vendor_language_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY vendor
    ADD CONSTRAINT vendor_language_id_fkey FOREIGN KEY (language_id) REFERENCES language(id);


--
-- Name: vendor_payment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY vendor
    ADD CONSTRAINT vendor_payment_id_fkey FOREIGN KEY (payment_id) REFERENCES payment_terms(id);


--
-- Name: vendor_taxzone_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY vendor
    ADD CONSTRAINT vendor_taxzone_id_fkey FOREIGN KEY (taxzone_id) REFERENCES tax_zones(id);


SET search_path = tax, pg_catalog;

--
-- Name: report_headings_category_id_fkey; Type: FK CONSTRAINT; Schema: tax; Owner: postgres
--

ALTER TABLE ONLY report_headings
    ADD CONSTRAINT report_headings_category_id_fkey FOREIGN KEY (category_id) REFERENCES report_categories(id);


--
-- Name: report_variables_heading_id_fkey; Type: FK CONSTRAINT; Schema: tax; Owner: postgres
--

ALTER TABLE ONLY report_variables
    ADD CONSTRAINT report_variables_heading_id_fkey FOREIGN KEY (heading_id) REFERENCES report_headings(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

