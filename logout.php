<?php
require_once 'includes/session.php';

// Logi kasutaja välja
logout();

// Seadista teade
set_flash_message('success', 'Olete edukalt välja logitud.');

// Suuna avalehele
header("Location: index.php");
exit;