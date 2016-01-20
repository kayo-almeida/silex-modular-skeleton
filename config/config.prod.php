<?php
/*
 * ATENÇÃO
 *
 * - Caso esteja em desenvolvimento crie o arquivo "config.dev.php"
 * - Caso esteja em testes crie o arquivo "config.hom.php"
 * - Atenção ao alterar esse arquivo quando o site estiver publicado
 * - Caso seu GIT seja público, coloque esse arquivo em .gitignore
 * - Compartilhe os dados desse arquivo apenas com pessoas de confiança
 */

// URL
define( "APPURL", "http://localhost/silex-modular-skeleton/public" );

// BANCO DE DADOS
define( "DB_DRIVER", "mysql" );
define( "DB_HOST", "localhost" );
define( "DB_NAME", "cms" );
define( "DB_USERNAME", "root" );
define( "DB_PASSWORD", "root" );
define( "DB_CHARSET", "utf8" );
define( "DB_COLLATION", "utf8_unicode_ci" );
define( "DB_PREFIX", "cms_" );

// E-MAIL
define("SMTP_HOST", "smtp.gmail.com");
define("SMTP_PORT", 465);
define("SMTP_USERNAME", "teste@gmail.com");
define("SMTP_PASSWORD", "teste123");
define("SMTP_ENCRYPTION", "ssl");
define("SMTP_AUTH_MODE", null);
define("SMTP_FROM", "teste@gmail.com");