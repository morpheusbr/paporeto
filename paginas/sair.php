<?php
$db= new SQLite('site');
if(User(MeuID())):
$ex=$db->statement("UPDATE sessao SET token=? WHERE id=?",array('',MeuID()));
else:
$db->statement("DELETE FROM sessao WHERE id=?",array(MeuID()));
endif;	
$db->delete('online', 'usuario',MeuID());
Sessao::destroySession();
Cookie::clear('lord_token');
header("Location: index");