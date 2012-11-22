<?php
/**
 * Created by 23rd and Walnut
 * www.23andwalnut.com
 * User: Saleem El-Amin
 * Date: Jun 15, 2010
 * Time: 5:53:36 PM
 */
?>

<?php

if (!isset($columns))
    $columns = null;

$model = explode('Controller', get_class($this));
$model = strtolower($model[0]);

$dir = "ds/logo/";

foreach ($client as &$detail)
    if (strlen($detail) <= 0)
        $detail = '-'

?>

<div id="page-content-outer">
    <div id="page-content" class="wrapper content admin">
        <div class="info-bar">
            <h5 class="breadcrumbs secondary"><a
                    href="<?php echo $breadcrumbs[1][1]; ?>"><?php echo $breadcrumbs[1][0];?></a></h5>
            <h1 class="title"><?php echo $breadcrumbs[0][0]; ?></h1>
        </div>

        <div class="inner">

            <div class="client-details">
                <ul class="client-details-list">
                    <li>
                        <span><?php echo $lang["lang_contact_person"] ?>:</span>
                    <?php echo $client['contact_person']; ?>
                    </li>
                    <li>
                        <span>Email:</span>
                    <?php echo $client['contact_email']; ?>
                    </li>
                    <li>
                        <span><?php echo $lang["lang_phone"] ?>:</span>
                    <?php echo $client['contact_phone']; ?>
                    </li>
                    <li>
                        <span><?php echo $lang["lang_adress"] ?>:</span>
                    <?php echo $client['address_line_1']; ?>
                    </li>
                    <li>
                        <span><?php echo $lang["lang_adress_line_two"] ?>:</span>
                    <?php echo $client['address_line_2']; ?>
                    </li>
                    <li>
                        <span>Created:</span>
                    <?php echo date("F j, Y - g:i a", $client['created']); ?>
                    </li>

                <?php if (is_array($client['additional_contacts'])): ?>
                    <li>&nbsp;</li>
                <?php foreach ($client['additional_contacts'] as $additional_contact): ?>
                    <li>
                        <span>Additional Contact:</span>
                    <?php echo $additional_contact; ?>
                    </li>
                <?php endforeach; ?>
                <?php endif; ?>

                </ul>
                <div class="client-actions">
                    <a class="small-button"
                       href="<?php echo $this->redirect("clients/credentials/" . $client['id'], true); ?>">
                        <span><?php echo $lang["lang_login_reset"] ?></span>
                    </a>
                    
                    <?
					if( $_SESSION['group_id'] == 0 ) {
					?>
					<a class="small-button"
                       href="<?php echo $this->redirect("clients/edit/myprofile", true); ?>">
                        <span><?php echo $lang["lang_edit"] ?></span>
                    </a>
					<?
					}
					else {
					?>
                    <a class="small-button"
                       href="<?php echo $this->redirect("clients/edit/" . $client['id'], true); ?>">
                        <span><?php echo $lang["lang_edit"] ?></span>
                    </a>
					<?
					}
					?>
                    <a class="small-button danger"
                       href="<?php echo $this->redirect("clients/delete/" . $client['id'], true); ?>">
                        <span><?php echo $lang["lang_delete"] ?></span>
                    </a>
                </div>
            </div>
            
            <div id="clientLogo" style="text-align: center;"><img src="<? echo $dir . $_SESSION['logo_id']; ?>" width="200" /></div>
            
            <div class="client-projects">
            <?php
            if (is_array($projects) > 0)
            {
                $data['page'] = $projects;
                $columns = array(
                    'name' => $lang["lang_project_name"],
                    'client_name' => 'Client',
                    'duration' => $lang["lang_duration"],
                    'progress' => $lang["lang_progress"]);
                $this->loadLibrary('formatdata.class');
                $data['page'] = FormatData::format(array('progress' => 'percentage'), $data['page']);
                $details_link = "index.php?a=projects/view/";
                $details_id_field = 'id';
                $this->set('details_link', $details_link);
                $this->set('details_id_field', $details_id_field);
                $this->set('data', $data);
                $this->set('columns', $columns);
                echo "<h3 class='project-list'>".$lang["lang_projects"]."</h3>";
                $this->loadView('fluid-list', false);
            }
            else
            {
                echo "<div class='alert notice no-projects'>No active projects</div>";
            }


            if (is_array($invoices))
            {
                $data['page'] = $invoices;
                $columns = array(
                    'invoice_number' =>  $lang["lang_numb"].'&nbsp;'.$lang["lang_invoice"] ,
                    'client_name' => 'Client',
                    'date_of_issue' => $lang["lang_issue_date"],
                    'due_date' => $lang["lang_due_date"],
                    'total' => 'Total');
                $details_link = "index.php?a=invoices/view/";
                $details_id_field = 'id';
                $this->set('data', $data);
                $this->set('columns', $columns);
                $this->set('details_link', $details_link);
                $this->set('details_id_field', $details_id_field);
                echo "<h3 class='invoice-list'>".$lang["lang_invoices"]."</h3>";
                $this->loadView('fluid-list', false);
            }
            else
            {
                echo "<div class='alert notice no-invoices'>".$lang["lang_no_invoice_items"]."</div>";
            }

            ?>
            </div>

        </div>


    </div>