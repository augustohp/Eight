<?php
namespace Eight\Database\Schema;

class A extends Base
{
    public function getVersion()
    {
        return 1;
    }
    
    public function getSql()
    {
        $s   = array();
        $s[] = "CREATE  TABLE IF NOT EXISTS `owner` (
                  `id` INT NOT NULL AUTO_INCREMENT ,
                  `name` VARCHAR(255) NULL ,
                  `salt` VARCHAR(255) NULL ,
                  PRIMARY KEY (`id`) ,
                  UNIQUE INDEX `UNQ_LABEL` (`name` ASC, `salt` ASC) ,
                  INDEX `IDX_NAME_SALT` USING HASH (`name`(5) ASC, `salt`(5) ASC) )
                ENGINE = InnoDB
                DEFAULT CHARACTER SET = utf8
                COLLATE = utf8_bin;";

        $s[] = "CREATE  TABLE IF NOT EXISTS `text` (
                  `id` INT NOT NULL AUTO_INCREMENT ,
                  `owner_id` INT NOT NULL ,
                  `title` VARCHAR(255) NULL ,
                  `content` TEXT NULL ,
                  `created` DATETIME NOT NULL ,
                  PRIMARY KEY (`id`) ,
                  INDEX `FK_OWNER` (`owner_id` ASC) ,
                  INDEX `FK_CREATED` USING BTREE (`created` ASC) ,
                  CONSTRAINT `FK_OWNER`
                    FOREIGN KEY (`owner_id` )
                    REFERENCES `eight`.`owner` (`id` )
                    ON DELETE NO ACTION
                    ON UPDATE NO ACTION)
                ENGINE = InnoDB
                DEFAULT CHARACTER SET = utf8
                COLLATE = utf8_bin;";
                
        $s[] = "CREATE  TABLE IF NOT EXISTS `version` (
                  `database` INT NULL ,
                  `api` VARCHAR(10) NULL )
                ENGINE = InnoDB
                DEFAULT CHARACTER SET = utf8
                COLLATE = utf8_bin;";
        return $s;
    }
}