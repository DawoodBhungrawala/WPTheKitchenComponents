<?php
get_header();
if (!is_user_logged_in()) { auth_redirect(); exit; }
while(have_posts()):the_post();
$id=get_the_ID();
$status=get_post_meta($id,'_tkc_report_status',true)?:'completed';
$project=get_post_meta($id,'_tkc_report_project',true);
$scope=get_post_meta($id,'_tkc_report_scope',true);
$prepared=get_post_meta($id,'_tkc_report_prepared_by',true);
$summary=get_post_meta($id,'_tkc_report_summary',true);
$risks=array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',get_post_meta($id,'_tkc_report_risks',true))));
$recommendations=array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',get_post_meta($id,'_tkc_report_recommendations',true))));
$evidence=array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',get_post_meta($id,'_tkc_report_evidence_urls',true))));
$progress=(int)get_post_meta($id,'_tkc_report_progress',true);
$metrics=array('Completed items'=>(int)get_post_meta($id,'_tkc_report_completed',true),'Issues found'=>(int)get_post_meta($id,'_tkc_report_issues',true),'Issues closed'=>(int)get_post_meta($id,'_tkc_report_closed',true),'Actions required'=>(int)get_post_meta($id,'_tkc_report_actions',true));
$signed=get_post_meta($id,'_tkc_report_signed_off',true)==='yes';
$archive_page=get_pages(array('meta_key'=>'_wp_page_template','meta_value'=>'template-reports.php','number'=>1));
$archive_url=$archive_page?get_permalink($archive_page[0]->ID):home_url('/');
?>
<main class="tkc-single-report">
 <div class="tkc-report-shell">
  <nav class="tkc-report-topnav"><a href="<?php echo esc_url($archive_url); ?>">← All Reports</a><button type="button" class="tkc-print-button" onclick="window.print()">Print / PDF</button></nav>
  <header class="tkc-single-report__header">
   <span class="tkc-kicker">Website Report</span><h1><?php the_title(); ?></h1><?php if($summary):?><p class="tkc-report-lead"><?php echo esc_html($summary); ?></p><?php endif; ?>
   <div class="tkc-report-meta-grid">
     <div><span>Project</span><strong><?php echo esc_html($project ?: get_bloginfo('name')); ?></strong></div>
     <div><span>Prepared by</span><strong><?php echo esc_html($prepared ?: get_the_author()); ?></strong></div>
     <div><span>Date</span><strong><?php echo esc_html(get_the_date('j M Y')); ?></strong></div>
     <div><span>Scope</span><strong><?php echo esc_html($scope ?: 'Website updates'); ?></strong></div>
   </div>
   <div class="tkc-report-status-line"><span class="tkc-status tkc-status--<?php echo esc_attr($status); ?>"><?php echo esc_html(tkc_report_status_label($status)); ?></span><span><?php echo esc_html($progress); ?>% progress</span></div>
  </header>

  <section class="tkc-report-summary-box"><p><?php echo nl2br(esc_html($summary)); ?></p></section>

  <section class="tkc-report-metrics">
   <div><strong><?php echo esc_html($progress); ?>%</strong><span>Overall progress</span></div>
   <?php foreach($metrics as $label=>$value): ?><div><strong><?php echo esc_html($value); ?></strong><span><?php echo esc_html($label); ?></span></div><?php endforeach; ?>
  </section>

  <div class="tkc-report-layout">
   <aside class="tkc-report-sidebar">
    <span class="tkc-kicker">Archive</span><h2>Recent reports</h2>
    <?php $recent=new WP_Query(array('post_type'=>'tkc_report','post_status'=>'publish','posts_per_page'=>8,'post__not_in'=>array($id),'orderby'=>'date','order'=>'DESC')); if($recent->have_posts()):while($recent->have_posts()):$recent->the_post(); ?>
      <a href="<?php the_permalink(); ?>"><small><?php echo esc_html(get_the_date('j M Y')); ?></small><strong><?php the_title(); ?></strong></a>
    <?php endwhile;wp_reset_postdata();else:?><p>No older reports yet.</p><?php endif; ?>
   </aside>
   <article class="tkc-report-content">
    <div class="tkc-report-section-title"><div><span class="tkc-kicker">Change Log</span><h2>Report details</h2></div></div>
    <div class="tkc-report-richtext"><?php the_content(); ?></div>

    <?php if(has_post_thumbnail() || $evidence): ?>
    <section class="tkc-evidence"><span class="tkc-kicker">See It</span><h2>Evidence</h2><div class="tkc-evidence-grid">
      <?php if(has_post_thumbnail()): ?><figure><?php the_post_thumbnail('large'); ?><figcaption>Primary report evidence</figcaption></figure><?php endif; ?>
      <?php foreach($evidence as $url): ?><figure><img src="<?php echo esc_url($url); ?>" alt="Report evidence" loading="lazy"><figcaption>Report evidence</figcaption></figure><?php endforeach; ?>
    </div></section>
    <?php endif; ?>

    <?php if($risks): ?><section class="tkc-risk-section"><span class="tkc-kicker">Open Items & Risks</span><h2>Needs attention</h2><div class="tkc-risk-table"><div class="tkc-risk-head"><span>Item</span><span>Severity</span><span>Owner</span></div><?php foreach($risks as $line):$parts=array_map('trim',explode('|',$line));?><div class="tkc-risk-row"><span><?php echo esc_html($parts[0]??''); ?></span><span><?php echo esc_html($parts[1]??'Review'); ?></span><span><?php echo esc_html($parts[2]??'Website'); ?></span></div><?php endforeach;?></div></section><?php endif; ?>

    <?php if($recommendations): ?><section class="tkc-next-section"><span class="tkc-kicker">Recommended Next</span><h2>Where to go from here</h2><ol><?php foreach($recommendations as $item):?><li><?php echo esc_html($item); ?></li><?php endforeach;?></ol></section><?php endif; ?>

    <section class="tkc-comments-section"><span class="tkc-kicker">Feedback</span><?php if(comments_open() || get_comments_number()): comments_template(); else: ?><h2>Comments</h2><p>Comments are disabled for this report. Enable Discussion → Allow comments when editing the report.</p><?php endif; ?></section>

    <section class="tkc-signoff" id="report-signoff"><span class="tkc-kicker">Approval</span><h2>Report sign-off</h2>
     <?php if($signed): ?><div class="tkc-signoff-success"><strong>Approved</strong><p>Signed by <?php echo esc_html(get_post_meta($id,'_tkc_report_signoff_name',true)); ?> on <?php echo esc_html(mysql2date('j M Y, g:i a',get_post_meta($id,'_tkc_report_signoff_date',true))); ?>.</p></div>
     <?php else: ?>
     <?php if(isset($_GET['signoff']) && $_GET['signoff']==='missing'):?><p class="tkc-form-error">Please complete the required fields and tick the approval checkbox.</p><?php endif;?>
     <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="tkc-signoff-form">
      <input type="hidden" name="action" value="tkc_report_signoff"><input type="hidden" name="report_id" value="<?php echo esc_attr($id); ?>"><?php wp_nonce_field('tkc_report_signoff_'.$id,'tkc_report_signoff_nonce'); ?>
      <div class="tkc-form-grid"><input required name="signoff_name" placeholder="Full name"><input required type="email" name="signoff_email" placeholder="Email"><input name="signoff_role" placeholder="Role / company"></div>
      <label class="tkc-check"><input type="checkbox" name="signoff_approved" value="yes" required> <span>I have reviewed this report and approve the work described.</span></label>
      <button type="submit" class="tkc-report-btn">Sign off report</button>
     </form><?php endif; ?>
    </section>
   </article>
  </div>
 </div>
</main>
<?php endwhile;get_footer(); ?>
