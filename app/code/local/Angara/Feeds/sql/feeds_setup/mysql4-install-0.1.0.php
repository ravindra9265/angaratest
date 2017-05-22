<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('feeds')};
CREATE TABLE {$this->getTable('feeds')} (
  `feeds_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `marketplace` varchar(100) NOT NULL default '',
  `status` tinyint(1) unsigned NOT NULL,
  `headings1` varchar(1300) NOT NULL default '',
  `headings2` varchar(4100) NOT NULL default '',
  `headings3` varchar(4100) NOT NULL default '',
  `column_1` varchar(50) NOT NULL default '',
  `column_2` varchar(50) NOT NULL default '',
  `column_3` varchar(50) NOT NULL default '',
  `column_4` varchar(50) NOT NULL default '',
  `column_5` varchar(50) NOT NULL default '',
  `column_6` varchar(50) NOT NULL default '',
  `column_7` varchar(50) NOT NULL default '',
  `column_8` varchar(50) NOT NULL default '',
  `column_9` varchar(50) NOT NULL default '',
  `column_10` varchar(50) NOT NULL default '',
  `column_11` varchar(50) NOT NULL default '',
  `column_12` varchar(50) NOT NULL default '',
  `column_13` varchar(50) NOT NULL default '',
  `column_14` varchar(50) NOT NULL default '',
  `column_15` varchar(50) NOT NULL default '',
  `column_16` varchar(50) NOT NULL default '',
  `column_17` varchar(50) NOT NULL default '',
  `column_18` varchar(50) NOT NULL default '',
  `column_19` varchar(50) NOT NULL default '',
  `column_20` varchar(50) NOT NULL default '',
  `column_21` varchar(50) NOT NULL default '',
  `column_22` varchar(50) NOT NULL default '',
  `column_23` varchar(50) NOT NULL default '',
  `column_24` varchar(50) NOT NULL default '',
  `column_25` varchar(50) NOT NULL default '',
  `column_26` varchar(50) NOT NULL default '',
  `column_27` varchar(50) NOT NULL default '',
  `column_28` varchar(50) NOT NULL default '',
  `column_29` varchar(50) NOT NULL default '',
  `column_30` varchar(50) NOT NULL default '',
  `column_31` varchar(50) NOT NULL default '',
  `column_32` varchar(50) NOT NULL default '',
  `column_33` varchar(50) NOT NULL default '',
  `column_34` varchar(50) NOT NULL default '',
  `column_35` varchar(50) NOT NULL default '',
  `column_36` varchar(50) NOT NULL default '',
  `column_37` varchar(50) NOT NULL default '',
  `column_38` varchar(50) NOT NULL default '',
  `column_39` varchar(50) NOT NULL default '',
  `column_40` varchar(50) NOT NULL default '',
  `column_41` varchar(50) NOT NULL default '',
  `column_42` varchar(50) NOT NULL default '',
  `column_43` varchar(50) NOT NULL default '',
  `column_44` varchar(50) NOT NULL default '',
  `column_45` varchar(50) NOT NULL default '',
  `column_46` varchar(50) NOT NULL default '',
  `column_47` varchar(50) NOT NULL default '',
  `column_48` varchar(50) NOT NULL default '',
  `column_49` varchar(50) NOT NULL default '',
  `column_50` varchar(50) NOT NULL default '',
  `column_51` varchar(50) NOT NULL default '',
  `column_52` varchar(50) NOT NULL default '',
  `column_53` varchar(50) NOT NULL default '',
  `column_54` varchar(50) NOT NULL default '',
  `column_55` varchar(50) NOT NULL default '',
  `column_56` varchar(50) NOT NULL default '',
  `column_57` varchar(50) NOT NULL default '',
  `column_58` varchar(50) NOT NULL default '',
  `column_59` varchar(50) NOT NULL default '',
  `column_60` varchar(50) NOT NULL default '',
  `column_61` varchar(50) NOT NULL default '',
  `column_62` varchar(50) NOT NULL default '',
  `column_63` varchar(50) NOT NULL default '',
  `column_64` varchar(50) NOT NULL default '',
  `column_65` varchar(50) NOT NULL default '',
  `column_66` varchar(50) NOT NULL default '',
  `column_67` varchar(50) NOT NULL default '',
  `column_68` varchar(50) NOT NULL default '',
  `column_69` varchar(50) NOT NULL default '',
  `column_70` varchar(50) NOT NULL default '',
  `column_71` varchar(50) NOT NULL default '',
  `column_72` varchar(50) NOT NULL default '',
  `column_73` varchar(50) NOT NULL default '',
  `column_74` varchar(50) NOT NULL default '',
  `column_75` varchar(50) NOT NULL default '',
  `column_76` varchar(50) NOT NULL default '',
  `column_77` varchar(50) NOT NULL default '',
  `column_78` varchar(50) NOT NULL default '',
  `column_79` varchar(50) NOT NULL default '',
  `column_80` varchar(50) NOT NULL default '',
  `column_81` varchar(50) NOT NULL default '',
  `column_82` varchar(50) NOT NULL default '',
  `column_83` varchar(50) NOT NULL default '',
  `column_84` varchar(50) NOT NULL default '',
  `column_85` varchar(50) NOT NULL default '',
  `column_86` varchar(50) NOT NULL default '',
  `column_87` varchar(50) NOT NULL default '',
  `column_88` varchar(50) NOT NULL default '',
  `column_89` varchar(50) NOT NULL default '',
  `column_90` varchar(50) NOT NULL default '',
  `column_91` varchar(50) NOT NULL default '',
  `column_92` varchar(50) NOT NULL default '',
  `column_93` varchar(50) NOT NULL default '',
  `column_94` varchar(50) NOT NULL default '',
  `column_95` varchar(50) NOT NULL default '',
  `column_96` varchar(50) NOT NULL default '',
  `column_97` varchar(50) NOT NULL default '',
  `column_98` varchar(50) NOT NULL default '',
  `column_99` varchar(50) NOT NULL default '',
  `column_100` varchar(50) NOT NULL default '',
  `column_101` varchar(50) NOT NULL default '',
  `column_102` varchar(50) NOT NULL default '',
  `column_103` varchar(50) NOT NULL default '',
  `column_104` varchar(50) NOT NULL default '',
  `column_105` varchar(50) NOT NULL default '',
  `column_106` varchar(50) NOT NULL default '',
  `column_107` varchar(50) NOT NULL default '',
  `column_108` varchar(50) NOT NULL default '',
  `column_109` varchar(50) NOT NULL default '',
  `column_110` varchar(50) NOT NULL default '',
  `column_111` varchar(50) NOT NULL default '',
  `column_112` varchar(50) NOT NULL default '',
  `column_113` varchar(50) NOT NULL default '',
  `column_114` varchar(50) NOT NULL default '',
  `column_115` varchar(50) NOT NULL default '',
  `column_116` varchar(50) NOT NULL default '',
  `column_117` varchar(50) NOT NULL default '',
  `column_118` varchar(50) NOT NULL default '',
  `column_119` varchar(50) NOT NULL default '',
  `column_120` varchar(50) NOT NULL default '',
  `column_121` varchar(50) NOT NULL default '',
  `column_122` varchar(50) NOT NULL default '',
  `column_123` varchar(50) NOT NULL default '',
  `column_124` varchar(50) NOT NULL default '',
  `column_125` varchar(50) NOT NULL default '',
  `column_126` varchar(50) NOT NULL default '',
  `column_127` varchar(50) NOT NULL default '',
  `column_128` varchar(50) NOT NULL default '',
  `column_129` varchar(50) NOT NULL default '',
  `column_130` varchar(50) NOT NULL default '',
  `column_131` varchar(50) NOT NULL default '',
  `column_132` varchar(50) NOT NULL default '',
  `column_133` varchar(50) NOT NULL default '',
  `column_134` varchar(50) NOT NULL default '',
  `column_135` varchar(50) NOT NULL default '',
  `column_136` varchar(50) NOT NULL default '',
  `column_137` varchar(50) NOT NULL default '',
  `column_138` varchar(50) NOT NULL default '',
  `column_139` varchar(50) NOT NULL default '',
  `column_140` varchar(50) NOT NULL default '',
  `column_141` varchar(50) NOT NULL default '',
  `column_142` varchar(50) NOT NULL default '',
  `column_143` varchar(50) NOT NULL default '',
  `column_144` varchar(50) NOT NULL default '',
  `column_145` varchar(50) NOT NULL default '',
  `column_146` varchar(50) NOT NULL default '',
  `column_147` varchar(50) NOT NULL default '',
  `column_148` varchar(50) NOT NULL default '',
  `column_149` varchar(50) NOT NULL default '',
  `column_150` varchar(50) NOT NULL default '',
  `column_151` varchar(50) NOT NULL default '',
  `column_152` varchar(50) NOT NULL default '',
  `column_153` varchar(50) NOT NULL default '',
  `column_154` varchar(50) NOT NULL default '',
  `column_155` varchar(50) NOT NULL default '',
  `column_156` varchar(50) NOT NULL default '',
  `column_157` varchar(50) NOT NULL default '',
  `column_158` varchar(50) NOT NULL default '',
  `column_159` varchar(50) NOT NULL default '',
  `column_160` varchar(50) NOT NULL default '',
  `column_161` varchar(50) NOT NULL default '',
  `column_162` varchar(50) NOT NULL default '',
  `column_163` varchar(50) NOT NULL default '',
  `column_164` varchar(50) NOT NULL default '',
  `column_165` varchar(50) NOT NULL default '',
  `column_166` varchar(50) NOT NULL default '',
  `column_167` varchar(50) NOT NULL default '',
  `column_168` varchar(50) NOT NULL default '',
  `column_169` varchar(50) NOT NULL default '',
  `column_170` varchar(50) NOT NULL default '',
  `column_171` varchar(50) NOT NULL default '',
  `column_172` varchar(50) NOT NULL default '',
  `column_173` varchar(50) NOT NULL default '',
  `column_174` varchar(50) NOT NULL default '',
  `column_175` varchar(50) NOT NULL default '',
  `column_176` varchar(50) NOT NULL default '',
  `column_177` varchar(50) NOT NULL default '',
  `column_178` varchar(50) NOT NULL default '',
  `column_179` varchar(50) NOT NULL default '',
  `column_180` varchar(50) NOT NULL default '',
  `column_181` varchar(50) NOT NULL default '',
  `column_182` varchar(50) NOT NULL default '',
  `column_183` varchar(50) NOT NULL default '',
  `column_184` varchar(50) NOT NULL default '',
  `column_185` varchar(50) NOT NULL default '',
  `column_186` varchar(50) NOT NULL default '',
  `column_187` varchar(50) NOT NULL default '',
  `column_188` varchar(50) NOT NULL default '',
  `column_189` varchar(50) NOT NULL default '',
  `column_190` varchar(50) NOT NULL default '',
  `column_191` varchar(50) NOT NULL default '',
  `column_192` varchar(50) NOT NULL default '',
  `column_193` varchar(50) NOT NULL default '',
  `column_194` varchar(50) NOT NULL default '',
  `column_195` varchar(50) NOT NULL default '',
  `column_196` varchar(50) NOT NULL default '',
  `column_197` varchar(50) NOT NULL default '',
  `column_198` varchar(50) NOT NULL default '',
  `column_199` varchar(50) NOT NULL default '',
  `column_200` varchar(50) NOT NULL default '',
  `column_201` varchar(50) NOT NULL default '',
  `column_202` varchar(50) NOT NULL default '',
  `column_203` varchar(50) NOT NULL default '',
  `column_204` varchar(50) NOT NULL default '',
  `column_205` varchar(50) NOT NULL default '',
  `column_206` varchar(50) NOT NULL default '',
  `column_207` varchar(50) NOT NULL default '',
  `column_208` varchar(50) NOT NULL default '',
  `column_209` varchar(50) NOT NULL default '',
  `column_210` varchar(50) NOT NULL default '',
  `column_211` varchar(50) NOT NULL default '',
  `column_212` varchar(50) NOT NULL default '',
  `column_213` varchar(50) NOT NULL default '',
  `column_214` varchar(50) NOT NULL default '',
  `column_215` varchar(50) NOT NULL default '',
  `column_216` varchar(50) NOT NULL default '',
  `column_217` varchar(50) NOT NULL default '',
  `column_218` varchar(50) NOT NULL default '',
  `column_219` varchar(50) NOT NULL default '',
  `column_220` varchar(50) NOT NULL default '',  
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`feeds_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 