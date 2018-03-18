<link href="css/sysgen_resumo.css" rel="stylesheet" type="text/css" />
<?php
/**
 * SysGen - Gerador de sistemas com Formdin Framework
 * https://github.com/bjverde/sysgen
 */
defined('APLICATIVO') or die();

$frm = new TForm(Message::GEN03_TITLE,200,900);
$frm->setFlat(true);
$frm->setMaximize(true);
$frm->setAutoSize(true);

if (!ArrayHelper::has('STEP2', $_SESSION[APLICATIVO]) ){
	$frm->redirect('gen02.php','Seu Mané teste as configurações de banco!!',true);
}

$frm->addGroupField('gpx1',Message::GEN02_GPX1_TITLE);
	$html = $frm->addHtmlField('conf','');
$frm->closeGroup();

$frm->addButton('Voltar', null, 'Voltar', null, null, true, false);
$frm->addButton('Limpar', null, 'Limpar', null, null, false, false);

$acao = isset($acao) ? $acao : null;
switch( $acao ) {
	case 'Voltar':
		$frm->redirect('gen02.php','Redirect realizado com sucesso.',true);
		break;
		//--------------------------------------------------------------------------------
	case 'Limpar':
		$frm->clearFields();
		break;
}


try {
	$html->add(TGeneratorHelper::showMsg(true,Message::GEN03_NEW_SYSTEM_OK));
	$html->add('<a href="'.TGeneratorHelper::getUrlNewSystem().'" target="_blank">'.TGeneratorHelper::getUrlNewSystem().'</a>');
	


	$listTables = TGeneratorHelper::loadTablesFromDatabase();
	$listTableNames = $listTables['TABLE_NAME'];
	foreach ($listTableNames as $key=>$table){
		$dao = TGeneratorHelper::getTDAOConect($table);
		$listFieldsTable = $dao->loadFieldsOneTableFromDatabase();
		$tableType = strtoupper($listTables['TABLE_TYPE'][$key]);
		$key = $key + 1;
		if($tableType == 'TABLE'){
			TGeneratorHelper::createFilesDaoVoFromTable($table, $listFieldsTable['COLUMN_NAME']);
			TGeneratorHelper::createFilesForms($table, $listFieldsTable['COLUMN_NAME']);
			$html->add('<br>'.$key.' - Criado Form, DAO e VO da tabela: '.$table);
		}else{
			TGeneratorHelper::createFilesDaoVoFromTable($table, $listFieldsTable['COLUMN_NAME']);
			$html->add('<br>'.$key.' - Criado DAO e VO da view: '.$table);
		}
		
		$gride = new TGrid( 'gd'      // id do gride
				           ,$key.' - Lista de campos da Tabela: '.$table   // titulo do gride
				           ,$listFieldsTable 	      // array de dados
				           );
		$gride->setCreateDefaultEditButton(false);
		$gride->setCreateDefaultDeleteButton(false);
		$frm->addHtmlField('gride'.$table,$gride);
	}

} catch (Exception $e) {
	echo $dao->getError();
	echo $e->getMessage();
}


$frm->show();
?>