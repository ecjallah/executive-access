<?php

define('MERCHANT_ID', 'ECOETG0002');
define('PROFILE_ID',  '2698F8DE-C903-4D36-94BB-DAEF12C7D430');
define('ACCESS_KEY',  '915664aad1c83f5e9ecc50c19180d75e');
define('SECRET_KEY',  '5d5b579bc30e42009e40392b0a9d3ce5615d9ae5bd3b4c51b00af31bee9cf4a46e40f2180c9643038db2abb2b067e64655fb01045ed24233aa307a9b227d1a24cc23c5b2caf94c2d9d1a350637b136bba6d617036dd44cbbb8493012fd513186216b9fa5774e4adfbe2d37e354e299225859eb44c167440791d9ace8cf0cc7fc');

// DF TEST: 1snn5n9w, LIVE: k8vif92e 
define('DF_ORG_ID', '1snn5n9w');

// PAYMENT URL
define('CYBS_BASE_URL', 'https://apitest.cybersource.com/silent');

define('PAYMENT_URL', CYBS_BASE_URL . '/pay');
// define('PAYMENT_URL', '/sa-sop/debug.php');

define('TOKEN_CREATE_URL', CYBS_BASE_URL . '/token/create');
define('TOKEN_UPDATE_URL', CYBS_BASE_URL . '/token/update');

// EOF