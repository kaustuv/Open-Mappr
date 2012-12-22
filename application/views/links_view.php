<!--ARROWS-->
<div id='arrow-container'>
  <div id='link-count'>Link <?php echo $current_link?> of <?php echo count($links_ar)?></div>
    <div id='from-arrow'>
    <div class='node-name' title="<?php echo $nodes_ar['from_node_description']?>">
      <div class='vert-align'>
        <?php echo $nodes_ar['from_node_name']?>
      </div>
    </div>
  </div>
  <div id='from-arrow-head'></div>
  <div id='link-arrow'>
    <div class='vert-align'>
      <div id='link-values-container'>
        <div id='link-values'>
          <div class='values'>
            <p><?php
             if($default['sign'] == 1)
             {
               echo '+';
             } else if($default['sign'] == 0)
             {
               echo '0';
             } else
             {
               echo '-';
             }
             ?>
            <p>
            <p><?php echo $default['strengthShort']?>&nbsp;<span class='grey'>|</span>&nbsp;<?php echo $default['strengthLong']?><p>
            <p><?php echo $default['strengthCertainty']?><p>
          </div>
          <div class='labels'>
            Sign
            <br/>
            Strength
            <br/>
            Certainty
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id='to-arrow-head'></div>
  <div id='to-arrow'>
    <div class='node-name' title="<?php echo $nodes_ar['to_node_description']?>">
      <div class='vert-align'>
        <?php echo $nodes_ar['to_node_name']?>
      </div>
    </div>
  </div>
</div>

<!--FORM-->
<?php // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => 'link-form');
echo form_open('links/verification/' . $current_link, $attributes); ?>

<?php if($form_success):?>
<div class='form-success'>Link Successfully Saved!</div>
<?php endif;?>
<!--COMMENTS-->
<div id='comments-box' class='input-box left-box dark-box'>
  <?php echo form_error('comments'); ?>
  <?php echo form_textarea( array( 'name' => 'comments', 'rows' => '5', 'cols' => '80', 'value' => set_value('comments',$default['comments']) ) )?>
  <label for="comments">Comment</label>
</div>

<!--SIGN-->
<div id='sign-box' class='input-box light-box'>
  <?php echo form_error('sign'); ?>
  <?php // Change the values in this array to populate your dropdown as required ?>
  <?php $options = array(
    '-1'    => '-',
    '0'    => '0',
    '1'    => '+'
    ); ?>
  <?php echo form_dropdown('sign', $options, set_value('sign',$default['sign']))?>
  <br/><label for="sign">Sign</label>
  <br>
  <div id='sign-key'>-&nbsp;&nbsp;&nbsp;&nbsp;0&nbsp;&nbsp;&nbsp;&nbsp;+</div>
</div>  

<!--STRENGTH-->
<div id='strength-box' class='input-box dark-box'>
  <div id='strength-dd-container'>
    <div id='strength-dds'>
      <div class='strength-dd'>
        <span class='strength-letter'>S</span><br/>
        <?php echo form_error('strengthShort'); ?>
        <?php // Change the values in this array to populate your dropdown as required ?>
        <?php $options = array(
          '1'    => '1',
          '2'    => '2',
          '3'    => '3',
          '4'    => '4',
          '5'    => '5'
          ); ?>
        <?php echo form_dropdown('strengthShort', $options, set_value('strengthShort',$default['strengthShort']))?>
      </div>
      <div class='strength-dd'>
        <span class='strength-letter'>L</span><br/>
        <?php echo form_error('strengthLong'); ?>
        <?php // Change the values in this array to populate your dropdown as required ?>
        <?php $options = array(
          '1'    => '1',
          '2'    => '2',
          '3'    => '3',
          '4'    => '4',
          '5'    => '5'
          ); ?>
        <?php echo form_dropdown('strengthLong', $options, set_value('strengthLong',$default['strengthLong']))?>
      </div>
    </div>
  </div>
  <div class='clearer'></div>
  <label for="strengthLong">Strength</label>
  <div id='strength-bell'></div>
</div>                                             

<!--CERTAINTY-->
<div id='certainty-box' class='input-box right-box dark-box'>
  <?php echo form_error('strengthCertainty'); ?>

  <?php // Change the values in this array to populate your dropdown as required ?>
  <?php $options = array(
    '0'    => '0',
    '1'    => '1',
    '2'    => '2'
    ); ?>
  <?php echo form_dropdown('strengthCertainty', $options, set_value('strengthCertainty',$default['strengthCertainty']))?>
  <br/>
  <label for="strengthCertainty">Certainty</label>
  <div id='certainty-bell'></div>
</div>                                             

<!--SUBMIT BUTTONS-->
<p>
  <?php if($current_link != count($links_ar)):?>
    <?php $data = array(
      'class'=>'submit-but',
      'name'=>'submit',
      'value'=>'Next Link');
      ?>
    <?php echo form_submit($data); ?>
  <?php endif;?>
  <?php $data = array(
    'class'=>'submit-but',
    'name'=>'submit',
    'value'=>'Save');
    ?>
  <?php echo form_submit($data); ?><div class='but-right'></div>
  <?php if($current_link != 1):?>
    <?php $data = array(
      'class'=>'submit-but',
      'name'=>'submit',
      'value'=>'Previous Link');
      ?>
    <?php echo form_submit($data); ?><div class='but-right'></div>
  <?php endif;?>
</p>

<?php echo form_close(); ?>
<div class='clearer'></div>
<!--LINKS TABLE-->
<div id='table-container'>
<div id='table-close'></div>
<div id='table-tab' class='submit-but'>Issue List</div>
<div id='total-rows'>Total Links:&nbsp;<span class='total'></span></div>
<div id='mean-strength'>Mean Strength:&nbsp;<span class='mean'></span></div>
<div id='table-cover'></div>
<div id='links-table-holder'>
  <table id="links-table"> 
    <thead> 
      <tr class='headers'>
        <th class='center'><div class='id'>Link ID</div></th> 
        <th class='center dark'><div class='modified'>Modified</div></th> 
        <th><div class='from'>From</div></th> 
        <th class='dark'><div class='to'>To</div></th> 
        <th class='center'><div class='sign'>Sign</div></th> 
        <th class='center dark'><div class='strength-short'>Strength Short</div></th> 
        <th class='center'><div class='strength-long'>Strength Long</div></th>
        <th class='center dark'><div class='certainty'>Certainty</div></th> 
      </tr> 
    </thead> 
    <tbody> 
    <?php foreach ($links_ar as $link):?>
      <?php
        $active = ''; 
        if($current_link == $link['id'])
        {
          $active = 'active';
        }
      ?>
      <?php if($link['id']%2 == 0):?>
      <tr class='even <?php echo $active ?>'> 
      <?php else:?>
      <tr class='odd <?php echo $active ?>'> 
      <?php endif;?>
        <td class='center'><div class='id'><?php echo $link['id']?></div></td>
        <td class='center dark'><div class='modified'><?php echo $link['modified']?></div></td>
        <td><div title='<?php echo $link["issueFromDescription"]?>' class='from'><?php echo $link['issueFrom']?></td>
        <td class='dark'><div title='<?php echo $link["issueToDescription"]?>' class='to'><?php echo $link['issueTo']?></td>
        <td class='center'><div class='sign'><?php echo $link['sign']?></td>
        <td class='center dark'><div class='strength-short'><?php echo $link['strengthShort']?></td>
        <td class='center'><div class='strength-long'><?php echo $link['strengthLong']?></td>
        <td class='center dark'><div class='certainty'><?php echo $link['strengthCertainty']?></td>
      </tr>
    <?php endforeach;?>
    </tbody>
  </table> 
</div>
</div>

