<?php
if (!defined('ABSPATH')) exit;
global $wpdb;
$is_officer = current_user_can('manage_options');

// Check for active surveys for current user role
$user_role = !empty(wp_get_current_user()->roles) ? wp_get_current_user()->roles[0] : '';
$active_surveys = Workedia_DB::get_surveys($user_role);

foreach ($active_surveys as $survey):
    // Check if already responded
    $responded = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$wpdb->prefix}workedia_survey_responses WHERE survey_id = %d AND user_id = %d", $survey->id, get_current_user_id()));
    if ($responded) continue;
?>
<div class="workedia-survey-card" style="background: #fffdf2; border: 2px solid #fef3c7; border-radius: 12px; padding: 25px; margin-bottom: 30px; position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; right: 0; background: #fbbf24; color: #78350f; font-size: 10px; font-weight: 800; padding: 4px 15px; border-radius: 0 0 0 12px;">استطلاع رأي هام</div>
    <h3 style="margin: 0 0 10px 0; color: #92400e;"><?php echo esc_html($survey->title); ?></h3>
    <p style="margin: 0 0 20px 0; font-size: 14px; color: #b45309;">يرجى المشاركة في هذا الاستطلاع القصير للمساهمة في تحسين جودة العملية المهنية.</p>

    <button class="workedia-btn" style="background: #d97706; width: auto;" onclick="workediaOpenSurveyModal(<?php echo $survey->id; ?>)">المشاركة الآن</button>
</div>

<!-- Survey Participation Modal -->
<div id="survey-participation-modal-<?php echo $survey->id; ?>" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 700px;">
        <div class="workedia-modal-header">
            <h3><?php echo esc_html($survey->title); ?></h3>
            <button class="workedia-modal-close" onclick="this.closest('.workedia-modal-overlay').style.display='none'">&times;</button>
        </div>
        <div class="workedia-modal-body" style="padding: 30px;">
            <div id="survey-questions-list-<?php echo $survey->id; ?>">
                <?php
                $questions = json_decode($survey->questions, true);
                foreach ($questions as $index => $q):
                ?>
                <div class="survey-question-block" style="margin-bottom: 25px; border-bottom: 1px solid #eee; padding-bottom: 15px;">
                    <div style="font-weight: 800; margin-bottom: 15px; color: var(--workedia-dark-color);"><?php echo ($index+1) . '. ' . esc_html($q); ?></div>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="survey_q_<?php echo $survey->id; ?>_<?php echo $index; ?>" value="ممتاز" required> ممتاز
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="survey_q_<?php echo $survey->id; ?>_<?php echo $index; ?>" value="جيد جداً"> جيد جداً
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="survey_q_<?php echo $survey->id; ?>_<?php echo $index; ?>" value="جيد"> جيد
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="survey_q_<?php echo $survey->id; ?>_<?php echo $index; ?>" value="مقبول"> مقبول
                        </label>
                        <label style="font-size: 13px; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                            <input type="radio" name="survey_q_<?php echo $survey->id; ?>_<?php echo $index; ?>" value="غير راض"> غير راض
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button class="workedia-btn" style="height: 45px; margin-top: 20px;" onclick="workediaSubmitSurveyResponse(<?php echo $survey->id; ?>, <?php echo count($questions); ?>)">إرسال الردود</button>
        </div>
    </div>
</div>
<?php endforeach; ?>

<script>
function workediaOpenSurveyModal(id) {
    document.getElementById('survey-participation-modal-' + id).style.display = 'flex';
}

function workediaSubmitSurveyResponse(surveyId, questionsCount) {
    const responses = [];
    for (let i = 0; i < questionsCount; i++) {
        const selected = document.querySelector(`input[name="survey_q_${surveyId}_${i}"]:checked`);
        if (!selected) {
            workediaShowNotification('يرجى الإجابة على جميع الأسئلة', true);
            return;
        }
        responses.push(selected.value);
    }

    const formData = new FormData();
    formData.append('action', 'workedia_submit_survey_response');
    formData.append('survey_id', surveyId);
    formData.append('responses', JSON.stringify(responses));
    formData.append('nonce', '<?php echo wp_create_nonce("workedia_survey_action"); ?>');

    fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            workediaShowNotification('تم إرسال ردودك بنجاح. شكراً لمشاركتك!');
            location.reload();
        } else {
            workediaShowNotification('فشل إرسال الردود: ' + res.data, true);
        }
    });
}
</script>

<?php if ($is_officer): ?>
<div class="workedia-card-grid" style="margin-bottom: 30px;">
    <div class="workedia-stat-card">
        <div style="font-size: 0.85em; color: var(--workedia-text-gray); margin-bottom: 10px; font-weight: 700;">إجمالي الأعضاء المسجلين</div>
        <div style="font-size: 2.5em; font-weight: 900; color: var(--workedia-primary-color);"><?php echo esc_html($stats['total_members'] ?? 0); ?></div>
    </div>
    <div class="workedia-stat-card">
        <div style="font-size: 0.85em; color: var(--workedia-text-gray); margin-bottom: 10px; font-weight: 700;">إجمالي الطاقم الإداري</div>
        <div style="font-size: 2.5em; font-weight: 900; color: var(--workedia-secondary-color);"><?php echo esc_html($stats['total_officers'] ?? 0); ?></div>
    </div>
</div>

<?php endif; ?>

<?php if ($is_officer): ?>
<div id="activity-logs" style="margin-top: 30px;">
    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:15px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <div>
                <h4 style="margin:0; font-size:16px;">سجل نشاطات النظام الشامل</h4>
                <div style="font-size:11px; color:#718096;">آخر 200 نشاط مسجل في النظام.</div>
            </div>
            <div style="display:flex; gap:10px;">
                <form method="get" style="display:flex; gap:5px;">
                    <input type="hidden" name="workedia_tab" value="summary">
                    <input type="text" name="log_search" value="<?php echo esc_attr($_GET['log_search'] ?? ''); ?>" placeholder="بحث في السجلات..." class="workedia-input" style="width:200px; padding:5px 10px; font-size:12px;">
                    <button type="submit" class="workedia-btn" style="width:auto; padding:5px 15px; font-size:12px;">بحث</button>
                </form>
                <button onclick="workediaDeleteAllLogs()" class="workedia-btn" style="background:#e53e3e; width:auto; font-size:12px; padding:5px 15px;">تفريغ السجل</button>
            </div>
        </div>
        <div class="workedia-table-container" style="margin:0; overflow-x:auto;">
            <table class="workedia-table" style="font-size:12px; width:100%;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:8px; width:140px;">الوقت</th>
                        <th style="padding:8px; width:120px;">المستخدم</th>
                        <th style="padding:8px; width:120px;">الإجراء</th>
                        <th style="padding:8px;">التفاصيل</th>
                        <th style="padding:8px; width:100px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $limit = 25;
                    $page_num = isset($_GET['log_page']) ? max(1, intval($_GET['log_page'])) : 1;
                    $offset = ($page_num - 1) * $limit;
                    $search = sanitize_text_field($_GET['log_search'] ?? '');
                    $all_logs = Workedia_Logger::get_logs($limit, $offset, $search);
                    $total_logs = Workedia_Logger::get_total_logs($search);
                    $total_pages = ceil($total_logs / $limit);

                    if (empty($all_logs)): ?>
                        <tr><td colspan="5" style="text-align:center; padding:20px; color:#94a3b8;">لا توجد سجلات تطابق البحث</td></tr>
                    <?php endif;

                    $appearance = Workedia_Settings::get_appearance();
                    foreach ($all_logs as $log):
                        $can_rollback = strpos($log->details, 'ROLLBACK_DATA:') === 0;
                        $details_display = $can_rollback ? 'عملية تتضمن بيانات للاستعادة' : esc_html($log->details);
                    ?>
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding:6px 8px; color: #718096;"><?php echo esc_html($log->created_at); ?></td>
                            <td style="padding:6px 8px; font-weight: 600;"><?php echo esc_html($log->display_name ?: 'نظام'); ?></td>
                            <td style="padding:6px 8px;"><span style="background:<?php echo $appearance['primary_color']; ?>15; color:<?php echo $appearance['primary_color']; ?>; padding:2px 6px; border-radius:4px; font-weight:700;"><?php echo esc_html($log->action); ?></span></td>
                            <td style="padding:6px 8px; color:#4a5568; line-height:1.4;"><?php echo mb_strimwidth($details_display, 0, 100, "..."); ?></td>
                            <td style="padding:6px 8px;">
                                <div style="display:flex; gap:5px;">
                                    <button onclick='workediaViewLogDetails(<?php echo json_encode($log); ?>)' class="workedia-btn workedia-btn-outline" style="padding:2px 8px; font-size:10px;">التفاصيل</button>
                                    <?php if ($can_rollback): ?>
                                        <button onclick="workediaRollbackLog(<?php echo $log->id; ?>)" class="workedia-btn" style="padding:2px 8px; font-size:10px; background:#38a169;">استعادة</button>
                                    <?php endif; ?>
                                    <button onclick="workediaDeleteLog(<?php echo $log->id; ?>)" class="workedia-btn" style="padding:2px 8px; font-size:10px; background:#e53e3e;">حذف</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($total_pages > 1): ?>
            <div style="display:flex; justify-content:center; gap:10px; margin-top:20px;">
                <?php if ($page_num > 1): ?>
                    <a href="<?php echo add_query_arg('log_page', $page_num - 1); ?>" class="workedia-btn workedia-btn-outline" style="width:auto; padding:5px 15px; text-decoration:none;">السابق</a>
                <?php endif; ?>
                <span style="align-self:center; font-size:13px;">صفحة <?php echo $page_num; ?> من <?php echo $total_pages; ?></span>
                <?php if ($page_num < $total_pages): ?>
                    <a href="<?php echo add_query_arg('log_page', $page_num + 1); ?>" class="workedia-btn workedia-btn-outline" style="width:auto; padding:5px 15px; text-decoration:none;">التالي</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
function workediaDownloadChart(chartId, fileName) {
    const canvas = document.getElementById(chartId);
    if (!canvas) return;
    const link = document.createElement('a');
    link.download = fileName + '.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
}

(function() {
    <?php if (!$is_officer): ?>
    return;
    <?php endif; ?>
    window.workediaCharts = window.workediaCharts || {};

    const initSummaryCharts = function() {
        if (typeof Chart === 'undefined') {
            setTimeout(initSummaryCharts, 200);
            return;
        }

        const chartOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } };

        const createOrUpdateChart = (id, config) => {
            if (window.workediaCharts[id]) {
                window.workediaCharts[id].destroy();
            }
            const el = document.getElementById(id);
            if (el) {
                window.workediaCharts[id] = new Chart(el.getContext('2d'), config);
            }
        };


    };

    if (document.readyState === 'complete') initSummaryCharts();
    else window.addEventListener('load', initSummaryCharts);
})();
</script>
