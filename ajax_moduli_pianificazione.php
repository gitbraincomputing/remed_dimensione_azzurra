<?
if(isset($_REQUEST['idregime'])){
	include_once('include/functions.inc.php');
	include_once('include/dbengine.inc.php');
	include_once('include/session.inc.php');
	$idregime=$_REQUEST['idregime'];
	$idimpegnativa=$_REQUEST['idimpegnativa'];
	$idcartella=$_REQUEST['idcartella'];
	$j=$_REQUEST['j'];
	$i=$_REQUEST['i'];
	$z=$_REQUEST['z'];
	$imp_associated=$_REQUEST['imp_associated'];
}

$conn = db_connect();
?>
<div class="titolo_pag" id="label<?=$j?>" style="margin:0px 0px 0px 15px;width:98%;background-color:<?=$colore?>"><h1 style="font-size:10px;">Moduli da associare all'impegnativa</h1></div>
<table id="table<?=$j?>" class="tablesorter" cellspacing="1" <? if($j>0){?>style="margin:0px 0px 15px 15px;width:98%;"<?}?>> 
	<thead> 
<tr>     
    <th width="10%">codice sgq</th> 
	<th>ant.</th> 
	<th width="30%">nome</th> 
    <th>pianifica</th> 
	<th>obbligatorio</th> 
	<th>figura responsabile</th> 
	<th >scadenza</th>
	<th >scad. ciclica</th>
	<th >scad. trattamenti</th>
	<th>scad. puntuale</th>
</tr> 
</thead> <?
	$query = "SELECT * from re_moduli_regimi_replica where idregime=$idregime and ((replica=2) or (replica=3)) order by id asc";
	$rs1 = mssql_query($query, $conn);
	if(!$rs1) error_message(mssql_error());
	$conta=mssql_num_rows($rs1);
		
			while($row1 = mssql_fetch_assoc($rs1))   
			{
				$cart=0;
				$idmodulo=$row1['idmodulo'];
				if($idcartella!=""){
					if($idimpegnativa==0) 
						$imp_query="id_impegnativa IS NULL";
						else
						$imp_query="id_impegnativa=$idimpegnativa";
					$query="SELECT * from istanze_testata WHERE id_modulo_padre=$idmodulo and id_cartella=$idcartella and $imp_query";
					$rs2 = mssql_query($query, $conn);				
					if (mssql_num_rows($rs2)>0){
						$compilato=1;}
						else{
						$compilato=0;
					}
					mssql_free_result($rs2);
					
					$query = "SELECT * from re_cartelle_pianificazione_1 where id_modulo_padre=$idmodulo and id_cartella=$idcartella and $imp_query";
					//echo($query);	
					$rs2 = mssql_query($query, $conn);				
					if (mssql_num_rows($rs2)>0){
						$cart=1;					
						$row2 = mssql_fetch_assoc($rs2);
						$idmodulo_id=$row2['id_modulo_versione'];
						$obbligatorio=trim($row2['obbligatorio']);					
						$scadenza=$row2['scadenza'];
						$trattamenti=$row2['trattamenti'];
						$data_fissa=formatta_data($row2['data_fissa']);
						$id_operatore=$row2['id_operatore'];
						if($data_fissa=="01/01/1900") $data_fissa="";
							} else {
						$query = "SELECT * from max_vers_moduli where idmodulo=$idmodulo order by versione DESC, idmoduloversione DESC";						
						$rs2 = mssql_query($query, $conn);
						$row2 = mssql_fetch_assoc($rs2);
						$idmodulo_id=$row2['id_modVers'];
						}
				}else{
					$query = "SELECT * from max_vers_moduli where idmodulo=$idmodulo order by versione DESC, idmoduloversione DESC";						
					$rs2 = mssql_query($query, $conn);
					$row2 = mssql_fetch_assoc($rs2);
					$idmodulo_id=$row2['id_modVers'];
				
				}
					$query = "SELECT top 1 * from moduli where id=$idmodulo_id";								
					$rs = mssql_query($query, $conn);
					if($row = mssql_fetch_assoc($rs)){   					
						$idmodulo_id=$row['id'];
						$nome=$row['nome'];
						$codice=$row['codice'];					
							if ($i>1)
							{
							$style="style=\"display:none\"";
							$class="riga_sottolineata_diversa hidden";
							$dis="disabled";							
							}
							else
							{
							$nodrag="nodrag=false";
							$style='';
							$class="odd";
							$dis="";
							}											
							$idcart=0;
							if ($cart==1) {					
								//$query4="SELECT uid as id_operatore FROM operatori WHERE (status =1) AND (cancella = 'n') AND (uid = $id_operatore)"; 
								$query4="SELECT uid as id_operatore FROM operatori WHERE (cancella = 'n') AND (uid = $id_operatore)";
							}else{
								$query4="SELECT TOP (1) id_modulo, id_regime, status, cancella, id, id_operatore, id_cartella FROM dbo.moduli_medici
							WHERE (status = 1) AND (cancella = 'n')AND (id_cartella = 0) AND (id_modulo = $idmodulo) AND (id_regime = $idregime) ORDER BY id DESC";								}
							
							//echo($query4);
							$rs4 = mssql_query($query4, $conn);							
							$medico="";
							if($row4=mssql_fetch_assoc($rs4)) $medico=$row4['id_operatore'];						
							mssql_free_result($rs4);
							
							$query3 = "SELECT * FROM regimi_moduli where idmodulo=$idmodulo and idregime=$idregime";							
							$rs3 = mssql_query($query3, $conn);
							if(!$rs3) error_message(mssql_error());
							
							if (mssql_num_rows($rs3)>0)							
							{
								if($row3 = mssql_fetch_assoc($rs3)){   									
									$obbligatorio_regime=trim($row3['obbligatorio']);
									if($cart==0){
										$obbligatorio=trim($row3['obbligatorio']);																
										$scadenza=$row3['scadenza'];
										$trattamenti=$row3['trattamenti'];
										if ($trattamenti==0) $trattamenti="";
										$data_fissa=formatta_data($row3['data_fissa']);
										if($data_fissa=="01/01/1900") $data_fissa="";
									}
								}								
								
								if(!empty($moduli_impegnative_in_pianificazione[$idimpegnativa]) && !empty($moduli_impegnative_in_pianificazione[$idimpegnativa][$idmodulo])) {									
									$presente_in_pianificazione_imp = $idmodulo_id == $moduli_impegnative_in_pianificazione[$idimpegnativa][$idmodulo];
								} else {
									$presente_in_pianificazione_imp = false;
								}
								
								if (($obbligatorio=="s")or($obbligatorio=="o"))
								{
									$checked="checked";
									$dis="disabled";									
								}									
								else
								{
									$checked="";
									$dis="";
								}?>
								<tr id="<?=$i?>">
								
								<td><span class="id_notizia_cat">
									<input type="hidden" name="selected<?=$i?>" value="<? if($j==0) echo("1"); else echo("0");?>" class="imp<?=$j?>" />
									<input type="hidden" name="mod_impegnativa<?=$i?>" value="<?=$idimpegnativa?>" />
									<input type="hidden" name="modulo<?=$i?>" value="<?=$idmodulo?>" />
									<input type="hidden" name="idmodulo<?=$i?>" value="<?=$idmodulo_id?>" />
									<?=$codice?>
									</span>
								</td>
								<td><div id="storico_residenza" class="aggiungi "><a href="#" onclick="javascript:prev('<?=$idmodulo_id?>');" title="anteprima modulo"><img src="images/view.png" /></a></div></td>
								<td><span class="id_notizia_cat"><?=$nome?></span></td>							
								<td>
									<div class="groups_1_one nomandatory">
									<? if  ($presente_in_pianificazione_imp && (($obbligatorio=='s')or($obbligatorio=='o')or($obbligatorio=='p'))){?>
											<input type="hidden" name="obbligatorio<?=$i?>" value="s" />
											<!--<input checked  type="checkbox" onclick="javascript:return false;" name="default<?=$i?>"/>-->
												<input  checked type="checkbox" onchange="javascript:a_d_medico(this,'<?=$i?>');" name="default<?=$i?>" />
									<?} else {?>	
											<input type="hidden" name="obbligatorio<?=$i?>" value="n" />	
											<input  type="checkbox" onchange="javascript:a_d_medico(this,'<?=$i?>');" name="default<?=$i?>" />
									<?}?>
									</div>
								</td>
								
								<td align="center">								
									<? if ($presente_in_pianificazione_imp && (($obbligatorio=='s')or($obbligatorio=='o')or($obbligatorio=='p'))){?>
										<div id="scelta_vis<?=$i?>" style="display:block;">
									<?}	else {?>
										<div id="scelta_vis<?=$i?>" style="display:none;">
									<?}?>								
									<select name="scelta<?=$i?>" style="width:150px">
									<? if (($obbligatorio=='s')or($obbligatorio=='o')or($obbligatorio=='f')) { ?>
										<option value="o">obbligatorio</option>
									<?} elseif(($obbligatorio=='p')){?>
										<option value="o">obbligatorio</option>
										<option value="p" selected>opzionale</option>
									<?} else {?>									
										<option value="o">obbligatorio</option>
										<option value="p" selected>opzionale</option>
									<?}	?>
									</select>
									</div>								
								</td>								
								<td>
									<?php 
									$display="";
									if($presente_in_pianificazione_imp && ($obbligatorio!='n')and($obbligatorio!='f')) {
										$display="block"
										?>
										<div id="medico_vis<?=$i?>" style="display:block;">
										<? }else {
										$display="none";
										
										}										
										if($medico!=""){
											$query = "SELECT nome from operatori where uid=$medico";						
											$rs2 = mssql_query($query, $conn);
											$row2 = mssql_fetch_assoc($rs2);
											$nome_medico=$row2['nome'];
										
										}else{
											$nome_medico="";
										}										
										?>										
										<div id="medico_vis<?=$i?>" style="display:<?=$display?>;">
										<input type="hidden" name="medico_old<?=$i?>" value="<?=$medico?>"/>
										<input type="hidden" name="medico_id_<?=$i?>" id="medico_id_<?=$i?>" value="<?=$medico?>"/>
										<input type="text" name="medico<?=$i?>" id="medico<?=$i?>" value="<?=$nome_medico?>"/><a href="javascript:void(0)" onclick="javascript:small_window('get_medici_popup.php?dest=<?=$i?>&parent=<?$medico?>&tipo=mod&idmodulo=<?=$idmodulo?>');" >seleziona</a>										
									</div>
								</td>
								<td><?
									$scadenza=trim($scadenza);
									$trattamenti=trim($trattamenti);
									$data_fissa=trim($data_fissa);
									?>
									<?php if($presente_in_pianificazione_imp && ($obbligatorio!='n')and($obbligatorio!='f')) $dis_scad="display:block;"; else $dis_scad="display:none;";?>
								<div id="scad_f<?=$i?>" style="<?=$dis_scad?>">
								<select id="scad_flg<?=$i?>" name="scad_flg<?=$i?>" style="width:100px;">
									<option value="n" <?if (($scadenza=='')and ($trattamenti=='') and ($data_fissa==''))echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>').slideUp();$('#trat_vis<?=$i?>').slideUp();$('#data_vis<?=$i?>').slideUp();">No</option>
									<option value="c" <?if ($scadenza!='') echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>').slideDown();$('#trat_vis<?=$i?>').slideUp();$('#data_vis<?=$i?>').slideUp();">Ciclica</option>
									<option value="t" <?if (($trattamenti!='') and($trattamenti!=0)) echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>').slideUp();$('#trat_vis<?=$i?>').slideDown();$('#data_vis<?=$i?>').slideUp();">Trattamenti</option>
									<option value="d" <?if ($data_fissa!='') echo("selected");?> onclick="javascript:$('#scad_vis<?=$i?>').slideUp();$('#trat_vis<?=$i?>').slideUp();$('#data_vis<?=$i?>').slideDown();">Data fissa</option>									
								</select>
								</div>
								</td>
								<td><?php if(($scadenza!='')and($obbligatorio!='n')) $dis_scad="display:block;"; else $dis_scad="display:none;";?>
									<div id="scad_vis<?=$i?>" style="<?=$dis_scad?>" class="nomandatory integer">
									<input type="text" name="scadenza<?=$i?>" value="<?=$scadenza?>" style="width:50px" />
									</div>
								</td>
								<td><?php if(($trattamenti!='')and ($trattamenti!=0) and($obbligatorio!='n')) $dis_scad="display:block;"; else $dis_scad="display:none;";?>
									<div id="trat_vis<?=$i?>" style="<?=$dis_scad?>" class="nomandatory integer">
									<input type="text" name="trattamenti<?=$i?>" value="<?=$trattamenti?>" style="width:50px"/>
									</div>
								</td>
								<td><?php if(($data_fissa!='')and($obbligatorio!='n')) $dis_scad="display:block;"; else $dis_scad="display:none;";?>
									<div id="data_vis<?=$i?>" style="<?=$dis_scad?>" class="nomandatory">
									<input type="text" class="campo_data" name="data_fissa<?=$i?>" value="<?=$data_fissa?>" style="width:100px" />
									</div>
								</td>
							</tr>
							<?							
							$i++;							
							}
					}
			}
			?>			
		</tbody>
	</table>


<div class="titolo_pag" id="label_t<?=$j?>" style="margin:0px 0px 0px 15px;width:98%;"><h1 style="font-size:10px;">Test Clinici associati all'impegnativa</h1></div>
<table id="table_t<?=$j?>" class="tablesorter" cellspacing="1" <? if($j>0){?>style="margin:0px 0px 15px 15px;width:98%;"<?}?>>	 
<thead>
	<tr> 
	<th>codice sgq</th> 	 
	<th>nome test clinico</th>
	<th>tipo</th>	
	<th>descrizione</th> 			
	<td>responsabile</td>
	<td>pianifica</td>	
	</tr> 
</thead> 
<tbody>	<?
	$query = "SELECT idtest,nome,codice,tipo_test,descrizione,test_ramificato from re_test_clinici where disponibile_in_lista_moduli=1 and stato=1 order by nome asc";	
	$rs1 = mssql_query($query, $conn);
	if(!$rs1) error_message(mssql_error());
	$conta=mssql_num_rows($rs1);
			while($row1 = mssql_fetch_assoc($rs1))   
			{					
				$test_associated=0;
				$idtest=$row1['idtest'];
				$codice=$row1['codice'];
				$nome=pulisci_lettura($row1['nome']);
				$tipo_test=$row1['tipo_test'];
				$descrizione=pulisci_lettura($row1['descrizione']);
				$test_ramificato=$row1['test_ramificato'];
				$operatore="";
				$nome_operatore="";
				$selected=0;
				if($idcartella!=""){
					$query="SELECT * FROM re_max_cartelle_pianificazione_test where id_cartella=$idcartella and id_impegnativa=$idimpegnativa and id_test=$idtest";
					//echo($query);
					$rs_test = mssql_query($query, $conn);
					if($row_t=mssql_fetch_assoc($rs_test)){
						$operatore=$row_t['id_operatore'];
						$selected=1;
						$query="SELECT nome, uid from operatori where uid=$operatore ";
						$rs_1 = mssql_query($query, $conn);
						if($row_o=mssql_fetch_assoc($rs_1)){
							$nome_operatore=$row_o['nome'];
						}	
					}
				}				
					?>					
					<tr> 
					 <td><input type="hidden" name="selected_t<?=$z?>" value="0" class="imp<?=$j?>" />
						<input type="hidden" name="mod_impegnativa_t<?=$z?>" value="<?=$idimpegnativa?>" />
						<input type="hidden" name="idtest<?=$z?>" value="<?=$idtest?>" /><?=$codice?>
					 </td> 
					 <td><?=$tipo_test?></td>	
					 <td><?=$nome?></td>
					 <td><?=$descrizione?></td> 
					 <td>
						<input type="hidden" name="medico_test_id_<?=$z?>" id="medico_test_id_<?=$z?>" value="<?=$operatore?>"/>
						<input type="text" name="medico_test<?=$z?>" id="medico_test<?=$z?>" value="<?=$nome_operatore?>"/><a href="javascript:void(0)" onclick="javascript:small_window('get_medici_popup.php?dest=<?=$z?>&parent=<?$operatore?>&tipo=test');" >seleziona</a>										
					</td>
					<td>
						<div class="groups_1_one nomandatory">
							<input type="checkbox" name="default_test<?=$z?>" <?if($selected)echo("checked");?> />
						</div>						
					</td>
				</tr> 
				<?				
				$z++;
			}?>
		</tbody> 
	</table>
<script type="text/javascript" language="javascript">
		$(document).ready(function() {			
					//$.mask.addPlaceholder('~',"[+-]");	
					//$(".campo_data").mask("99/99/9999");				
			
		});	
</script>		
<!-- fine test clinici -->
<!--</div>-->
