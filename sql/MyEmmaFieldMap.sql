CREATE TABLE IF NOT EXISTS civicrm_my_emma_field_map (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  account_id INT UNSIGNED NOT NULL,
  civicrm_field VARCHAR(256) NOT NULL,
  autocomplete_option_list INT unsigned NOT NULL default '0',
  location_type_id INT UNSIGNED NOT NULL default '0',
  my_emma_field varchar(256) NOT NULL,
  PRIMARY KEY (id),
  INDEX fk_account_idx (account_id ASC),
  CONSTRAINT fk_account_id
    FOREIGN KEY (account_id)
    REFERENCES civicrm_my_emma_account (id)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;