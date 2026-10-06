<?php
/**
 * Template Name: Website Reports
 */
get_header();
if (!is_user_logged_in()) { auth_redirect(); exit; }
$reports = new WP_Query(array('post_type'=>'tkc_report','post_status'=>'publish','posts_per_page'=>-1,'orderby'=>'date','order'=>'DESC'));
$total = (int)$reports->found_posts;
$completed=$progress=$review=0;$sum=0;
foreach ($reports->posts as $r) {
  $s=get_post_meta($r->ID,'_tkc_report_status',true) ?: 'completed';
  if($s==='completed')$completed++;
  if($s==='in-progress')$progress++;
  if(in_array($s,array('review','attention'),true))$review++;
  $sum+=(int)get_post_meta($r->ID,'_tkc_report_progress',true);
}
$overall=$total?round($sum/$total):0;
$latest=$total?$reports->posts[0]:null;
?>
<main class="tkc-reports-page">
 <div class="tkc-reports-shell">
  <header class="tkc-reports-hero">
   <div><span class="tkc-kicker">Website Work & Status</span><h1>Reports</h1><p>Track completed work, current progress, evidence, open items and recommended next steps in one place.</p></div>
   <div class="tkc-report-actions"><a class="tkc-report-btn tkc-report-btn--ghost" href="<?php echo esc_url(home_url('/')); ?>">View Website</a></div>
  </header>
  <?php if($latest): $ls=get_post_meta($latest->ID,'_tkc_report_status',true)?:'completed'; ?>
  <section class="tkc-report-latest">
    <div class="tkc-report-latest__top"><span class="tkc-status tkc-status--<?php echo esc_attr($ls); ?>"><?php echo esc_html(tkc_report_status_label($ls)); ?></span><span><?php echo esc_html(get_the_date('j M Y',$latest)); ?></span></div>
    <h2><?php echo esc_html(get_the_title($latest)); ?></h2>
    <p><?php echo esc_html(get_post_meta($latest->ID,'_tkc_report_summary',true)); ?></p>
    <a href="<?php echo esc_url(get_permalink($latest)); ?>">Open latest report →</a>
  </section>
  <?php endif; ?>
  <section class="tkc-report-stats">
    <div><strong><?php echo esc_html($total); ?></strong><span>Total reports</span></div>
    <div><strong><?php echo esc_html($completed); ?></strong><span>Completed</span></div>
    <div><strong><?php echo esc_html($progress); ?></strong><span>In progress</span></div>
    <div><strong><?php echo esc_html($review); ?></strong><span>Needs review</span></div>
    <div><strong><?php echo esc_html($overall); ?>%</strong><span>Average progress</span></div>
  </section>
  <section class="tkc-report-archive">
   <div class="tkc-report-section-title"><div><span class="tkc-kicker">Change Log</span><h2>Report archive</h2></div><p>Newest reports appear first.</p></div>
   <?php if($reports->have_posts()): ?>
   <div class="tkc-report-list">
    <?php while($reports->have_posts()):$reports->the_post();$id=get_the_ID();$s=get_post_meta($id,'_tkc_report_status',true)?:'completed';$scope=get_post_meta($id,'_tkc_report_scope',true);$summary=get_post_meta($id,'_tkc_report_summary',true);$prog=(int)get_post_meta($id,'_tkc_report_progress',true); ?>
      <a class="tkc-report-row" href="<?php the_permalink(); ?>">
       <div class="tkc-report-row__date"><span><?php echo esc_html(get_the_date('M')); ?></span><strong><?php echo esc_html(get_the_date('j')); ?></strong><small><?php echo esc_html(get_the_date('Y')); ?></small></div>
       <div class="tkc-report-row__body"><div class="tkc-report-row__meta"><span class="tkc-status tkc-status--<?php echo esc_attr($s); ?>"><?php echo esc_html(tkc_report_status_label($s)); ?></span><?php if($scope): ?><span><?php echo esc_html($scope); ?></span><?php endif; ?></div><h3><?php the_title(); ?></h3><p><?php echo esc_html($summary); ?></p><div class="tkc-progress"><span style="width:<?php echo esc_attr($prog); ?>%"></span></div></div>
       <span class="tkc-report-row__arrow">→</span>
      </a>
    <?php endwhile;wp_reset_postdata(); ?>
   </div>
   <?php else: ?><div class="tkc-report-empty"><h3>No reports published yet</h3><p>Published Website Reports will appear here automatically.</p></div><?php endif; ?>
  </section>
 </div>
</main>
<?php get_footer(); ?>
