-- updated_at trigger function
CREATE OR REPLACE FUNCTION updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- i18n_updated_at trigger function
CREATE OR REPLACE FUNCTION i18n_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    NEW.i18n_new=0;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- permissions
CREATE TABLE public.permissions (
    pr_id int GENERATED ALWAYS AS IDENTITY NOT NULL,
    pr_name varchar NOT NULL,
    pr_description varchar DEFAULT '' NOT NULL,
    pr_status int DEFAULT 1 NOT NULL,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT permissions_pk PRIMARY KEY (pr_id),
    CONSTRAINT permissions_uniq1 UNIQUE (pr_name)
);
CREATE TRIGGER trigger_permissions_updated_at
BEFORE UPDATE ON public.permissions
FOR EACH ROW
EXECUTE FUNCTION updated_at();

-- First permission to rule them all
INSERT INTO public.permissions (pr_name, pr_description) VALUES ('All','');

-- Roles
CREATE TABLE public.roles (
    ro_id int GENERATED ALWAYS AS IDENTITY NOT NULL,
    ro_name varchar NOT NULL,
    ro_description VARCHAR NOT NULL,
    ro_created_at timestamp default CURRENT_TIMESTAMP not NULL,
    constraint roles_pk primary key (ro_id),
    constraint roles_uniq1 unique (ro_name)
);
CREATE TRIGGER trigger_roles_updated_at
BEFORE UPDATE ON public.roles
FOR EACH ROW
EXECUTE FUNCTION updated_at();

-- First role to rule them all
INSERT INTO public.roles (ro_name,ro_description) VALUES ('Admin','Admin to rule them all!');

-- User table
CREATE TABLE public.users (
	us_id int GENERATED ALWAYS AS IDENTITY NOT NULL,
	us_email varchar NOT NULL,
	us_phone varchar NOT NULL,
    us_fullname varchar NOT NULL,
	us_password varchar NOT NULL,
	us_status varchar DEFAULT 0 NOT NULL,
    us_role int DEFAULT 0 NOT NULL,
    us_permissions TEXT DEFAULT NULL NULL,
	created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
	updated_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    FOREIGN KEY (us_role) REFERENCES roles(ro_id),
	CONSTRAINT users_pk PRIMARY KEY (us_id),
	CONSTRAINT users_uniq1 UNIQUE (us_email),
	CONSTRAINT users_uniq2 UNIQUE (us_phone)
);
-- updated_at trigger assign
CREATE TRIGGER trigger_users_updated_at
BEFORE UPDATE ON public.users
FOR EACH ROW
EXECUTE FUNCTION updated_at();

-- First user admin
INSERT INTO public.users (us_email,us_phone,us_fullname,us_password,us_status,us_role) VALUES ('admin@admin.com','+905004003020','Admin','admin*4321',1,1);

-- Roles-Permissions Group table
CREATE TABLE public.roles_permissions (
    ro_id int,
    pr_id int,
    CONSTRAINT roles_permissions_pk PRIMARY KEY (ro_id,pr_id),
    FOREIGN KEY (ro_id) REFERENCES roles(ro_id),
    FOREIGN KEY (pr_id) REFERENCES permissions(pr_id)
);

-- First Roles-Permissions Group 
INSERT INTO public.roles_permissions (ro_id,pr_id) VALUES (1,1);

-- Logs table
CREATE TABLE public.logs (
    lo_id int GENERATED ALWAYS AS IDENTITY NOT NULL,
    lo_subject varchar NOT NULL,
    lo_message varchar NOT NULL,
    lo_user int DEFAULT 0 NOT NULL,
    created_at timestamp default CURRENT_TIMESTAMP NOT null,
    constraint logs_pk primary key (lo_id)
);

-- First log
INSERT INTO public.logs (lo_subject,lo_message) VALUES ('SYSTEM','Sistem başarıyla kuruldu.');

CREATE TABLE public.files (
    fl_id int GENERATED ALWAYS AS IDENTITY NOT NULL,
    fl_name varchar NOT NULL,
    fl_description varchar NOT NULL,
    fl_url varchar NOT NULL,
    fl_type varchar NOT NULL,
    fl_size varchar NOT NULL,
    fl_creator varchar NOT NULL,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT files_pk PRIMARY KEY (fl_id),
    CONSTRAINT files_uniq1 UNIQUE (fl_name)
);
CREATE TRIGGER trigger_files_updated_at
BEFORE UPDATE ON public.files
FOR EACH ROW
EXECUTE FUNCTION updated_at();

CREATE TABLE public.i18n_words(
    i18n_id int GENERATED ALWAYS AS IDENTITY NOT NULL,
    i18n_name varchar NOT NULL,
    i18n_value text not null,
    i18n_language varchar DEFAULT 'tr',
    i18n_new int4 DEFAULT 0 NULL,
    created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    CONSTRAINT i18n_words_pk PRIMARY KEY (i18n_id),
    CONSTRAINT i18n_words_uniq1 unique (i18n_name,i18n_language)
);
CREATE TRIGGER trigger_i18n_words_updated_at
BEFORE UPDATE ON public.i18n_words
FOR EACH ROW
EXECUTE FUNCTION i18n_updated_at();

INSERT INTO public.i18n_words (i18n_name,i18n_value,i18n_language)VALUES('email_cant_empty_or_null','E-posta adresi alanı boş bırakılamaz!','tr');
INSERT INTO public.i18n_words (i18n_name,i18n_value,i18n_language)VALUES('phone_cant_empty_or_null','Telefon numarası alanı boş bırakılamaz!','tr');
INSERT INTO public.i18n_words (i18n_name,i18n_value,i18n_language)VALUES('password_cant_empty_or_null','Parola alanı boş bırakılamaz.','tr');
INSERT INTO public.i18n_words (i18n_name,i18n_value,i18n_language)VALUES('reassword_cant_empty_or_null','Parola tekrar alanı boş bırakılamaz.','tr');
INSERT INTO public.i18n_words (i18n_name,i18n_value,i18n_language)VALUES('password_not_match_repassword','Parola tekrarı parola ile uyuşmuyor!','tr');
INSERT INTO public.i18n_words (i18n_name,i18n_value,i18n_language)VALUES('created_user_successfully','Kullanıcı başarıyla oluşturuldu!','tr');
INSERT INTO public.i18n_words (i18n_name,i18n_value,i18n_language)VALUES('email_already_used','Bu e-posta adresi kullanımda!','tr');
INSERT INTO public.i18n_words (i18n_name,i18n_value,i18n_language)VALUES('404_not_found_title','404 - Sayfa bulunamadı','tr');
INSERT INTO public.i18n_words (i18n_name,i18n_value,i18n_language)VALUES('404_not_found_backtohome','Anasayfaya dön','tr');
INSERT INTO public.i18n_words (i18n_name,i18n_value,i18n_language)VALUES('404_not_found_message','Ziyaret etmek istediğiniz sayfa mevcut değil!','tr');

CREATE TABLE public.settings(
    st_id int GENERATED ALWAYS AS IDENTITY NOT NULL,
    st_name varchar NOT NULL,
    st_value varchar NOT NULL,
    st_created_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updated_at timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);
CREATE TRIGGER trigger_settings_updated_at
BEFORE UPDATE ON public.settings
FOR EACH ROW
EXECUTE FUNCTION updated_at();

INSERT INTO public.settings (st_name,st_value)VALUES('site_title','Admin Site');

-- create_user method
-- usage : select * from create_user();
CREATE OR REPLACE FUNCTION public.create_user(p_id integer, p_email character varying, p_phone character varying, p_fullname character varying, p_password character varying, p_repassword character varying, p_status integer DEFAULT 0, p_role integer DEFAULT 0)
 RETURNS TABLE(status_code integer, message text, _id integer)
 LANGUAGE plpgsql
AS $function$
DECLARE 
    error TEXT DEFAULT '';
declare _id int DEFAULT 0;
declare my_counter int DEFAULT 0;
begin
	
	if p_id is null THEN
	    IF p_email IS NULL OR p_email = '' THEN
	        error := 'email_cant_be_empty_or_null,';
	    END IF;
	    IF p_phone IS NULL OR p_phone = '' THEN
	        error := CONCAT(error, 'phone_cant_be_empty_or_null,');
	    END IF;
	    IF p_fullname IS NULL OR p_fullname = '' THEN
	        error := CONCAT(error, 'fullname_cant_be_empty_or_null,');
	    END IF;
	    IF p_password IS NULL OR p_password = '' THEN
	        error := CONCAT(error, 'password_cant_be_empty_or_null,');
	    END IF;
	    
	    IF p_repassword IS NULL OR p_repassword = '' THEN
	        error := CONCAT(error, 'repassword_cant_be_empty_or_null,');
	    END IF;
	    
	    select count(us_phone) into my_counter from users where us_phone=p_phone;
		if my_counter>0 then
			error := 'phone_already_used,';
		end if;
	   
		select count(us_email) into my_counter from users where us_email=p_email;
	   	if my_counter>0 then
			error := 'email_already_used,';
		end if;
		
		IF p_password <> p_repassword THEN
	        error := 'password_not_match_repassword,';
	    END IF;
	
	    IF error = '' then
	    
	        INSERT INTO public.users (
	            us_email,
	            us_phone,
	            us_fullname,
	            us_password,
	            us_status,
	            us_role
	        ) VALUES (
	            p_email,
	            p_phone,
	            p_fullname,
	            p_password,
	            p_status,
	            p_role
	        ) returning us_id into _id;
	       insert into public.users_roles (us_id,ro_id)VALUES(_id,p_role);
	       return query select 201 as status_code, 'created_user_successfully' as message, _id as id;
	    else
	    	error := rtrim(error,',');
	        return query select 400 as status_code, error as message, -1 as id;
	    END IF;
	   
	else
	
	IF p_email IS NULL OR p_email = '' THEN
	        error := 'email_cant_be_empty_or_null,';
	    END IF;
	    IF p_phone IS NULL OR p_phone = '' THEN
	        error := CONCAT(error, 'phone_cant_be_empty_or_null,');
	    END IF;
	    IF p_fullname IS NULL OR p_fullname = '' THEN
	        error := CONCAT(error, 'fullname_cant_be_empty_or_null,');
	    END IF;
	    IF p_password IS NULL OR p_password = '' THEN
	        error := CONCAT(error, 'password_cant_be_empty_or_null,');
	    END IF;
	    
	    IF p_repassword IS NULL OR p_repassword = '' THEN
	        error := CONCAT(error, 'repassword_cant_be_empty_or_null,');
	    END IF;
	    
	    select count(us_phone) into my_counter from users where us_phone=p_phone and us_id<>p_id;
		if my_counter>0 then
			error := 'phone_already_used,';
		end if;
	   
		select count(us_email) into my_counter from users where us_email=p_email and us_id<>p_id;
	   	if my_counter>0 then
			error := 'email_already_used,';
		end if;
		
		IF p_password <> p_repassword THEN
	        error := 'password_not_match_repassword,';
	    END IF;
	
	    IF error = '' then
	        update public.users set
	            us_email=p_email,
	            us_phone=p_phone,
	            us_fullname=p_fullname,
	            us_password=p_password
	        where us_id=p_id;
	       return query select 200 as status_code, 'update_user_successfully' as message, _id as id;
	    else
	    	error := rtrim(error,',');
	        return query select 400 as status_code, error as message, -1 as id;
	    END IF;
	   
	 end if;
END;
$function$
;
