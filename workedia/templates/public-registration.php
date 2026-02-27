<?php if (!defined('ABSPATH')) exit; ?>

<div class="workedia-reg-wrapper" dir="rtl">
    <div class="workedia-reg-container" id="reg-container">
        <!-- Stage 1: Name -->
        <div class="reg-stage active" id="stage-1">
            <h2 class="reg-title">أهلاً بك، لنبدأ بالتعرف عليك</h2>
            <div class="reg-form-group">
                <input type="text" id="reg_first_name" class="reg-input" placeholder="الاسم الأول" required>
            </div>
            <div class="reg-form-group">
                <input type="text" id="reg_last_name" class="reg-input" placeholder="اسم العائلة" required>
            </div>
            <button class="reg-btn" onclick="nextStage(1)">متابعة</button>
        </div>

        <!-- Stage 2: Gender & YOB -->
        <div class="reg-stage" id="stage-2">
            <h2 class="reg-title">قليلاً من التفاصيل الإضافية</h2>
            <div class="reg-form-group">
                <select id="reg_gender" class="reg-input">
                    <option value="male">ذكر</option>
                    <option value="female">أنثى</option>
                </select>
            </div>
            <div class="reg-form-group">
                <input type="number" id="reg_yob" class="reg-input" placeholder="سنة الميلاد (مثلاً: 1990)" min="1900" max="<?php echo date('Y'); ?>" required>
            </div>
            <div class="reg-nav">
                <button class="reg-btn-link" onclick="prevStage(2)">السابق</button>
                <button class="reg-btn" onclick="nextStage(2)">متابعة</button>
            </div>
        </div>

        <!-- Stage 3: Email & Username -->
        <div class="reg-stage" id="stage-3">
            <h2 class="reg-title">كيف يمكننا التواصل معك؟</h2>
            <div class="reg-form-group">
                <input type="email" id="reg_email" class="reg-input" placeholder="البريد الإلكتروني" oninput="debounceValidation('email')" required>
                <div id="email-validation-msg" class="reg-validation-msg"></div>
            </div>
            <div class="reg-form-group">
                <input type="text" id="reg_username" class="reg-input" placeholder="اسم المستخدم" oninput="debounceValidation('username')" required>
                <div id="username-validation-msg" class="reg-validation-msg"></div>
            </div>
            <div class="reg-nav">
                <button class="reg-btn-link" onclick="prevStage(3)">السابق</button>
                <button class="reg-btn" id="btn-stage-3" onclick="nextStage(3)">متابعة</button>
            </div>
        </div>

        <!-- Stage 4: Password -->
        <div class="reg-stage" id="stage-4">
            <h2 class="reg-title">قم بتأمين حسابك</h2>
            <div class="reg-form-group">
                <input type="password" id="reg_password" class="reg-input" placeholder="كلمة المرور" required>
            </div>
            <div class="reg-form-group">
                <input type="password" id="reg_password_confirm" class="reg-input" placeholder="تأكيد كلمة المرور" required>
            </div>
            <div id="pass-error" class="reg-error"></div>
            <div class="reg-nav">
                <button class="reg-btn-link" onclick="prevStage(4)">السابق</button>
                <button class="reg-btn" onclick="nextStage(4)">تأكيد وإرسال الرمز</button>
            </div>
        </div>

        <!-- Stage 5: OTP -->
        <div class="reg-stage" id="stage-5">
            <h2 class="reg-title">تحقق من بريدك الإلكتروني</h2>
            <p class="reg-subtitle">لقد أرسلنا رمزاً مكوناً من 6 أرقام إلى بريدك.</p>
            <div class="reg-form-group">
                <input type="text" id="reg_otp" class="reg-input otp-input" placeholder="000000" maxlength="6">
            </div>
            <div id="otp-error" class="reg-error"></div>
            <button class="reg-btn" onclick="verifyOTP()">تحقق الآن</button>
            <p class="reg-resend">لم يصلك الرمز؟ <a href="javascript:void(0)" onclick="sendOTP()">إعادة إرسال</a></p>
        </div>

        <!-- Stage 6: Welcome & Profile Photo -->
        <div class="reg-stage" id="stage-6">
            <h2 class="reg-title">أهلاً بك في عائلتنا!</h2>
            <p class="reg-subtitle">تم التحقق من حسابك بنجاح. ما رأيك بإضافة صورة شخصية؟</p>

            <div class="reg-photo-upload" onclick="document.getElementById('reg_photo').click()">
                <div id="photo-preview">📸</div>
                <input type="file" id="reg_photo" style="display:none" accept="image/*" onchange="previewPhoto(this)">
            </div>

            <button class="reg-btn" onclick="completeRegistration()">إتمام التسجيل والدخول</button>
            <button class="reg-btn-link" onclick="completeRegistration()">تخطي الآن</button>
        </div>
    </div>
</div>

<style>
.workedia-reg-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 600px;
    padding: 20px;
    font-family: 'Rubik', sans-serif;
}
.workedia-reg-container {
    background: #fff;
    width: 100%;
    max-width: 450px;
    padding: 40px;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
    min-height: 400px;
}
.reg-stage {
    display: none;
    flex-direction: column;
    animation: fadeIn 0.5s ease-out;
}
.reg-stage.active {
    display: flex;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.reg-title {
    font-size: 1.5em;
    font-weight: 800;
    margin-bottom: 30px;
    text-align: center;
    color: var(--workedia-dark-color);
}
.reg-subtitle {
    text-align: center;
    color: #64748b;
    margin-bottom: 25px;
    font-size: 0.95em;
}
.reg-form-group {
    margin-bottom: 20px;
}
.reg-input {
    width: 100%;
    padding: 15px 20px;
    border: 2px solid #f1f5f9;
    border-radius: 12px;
    font-size: 1em;
    transition: 0.3s;
    outline: none;
}
.reg-input:focus {
    border-color: var(--workedia-primary-color);
    background: #fff;
}
.reg-btn {
    background: var(--workedia-primary-color);
    color: #fff;
    border: none;
    padding: 15px;
    border-radius: 12px;
    font-size: 1.1em;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
}
.reg-btn:hover {
    opacity: 0.9;
    transform: translateY(-2px);
}
.reg-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px;
}
.reg-btn-link {
    background: none;
    border: none;
    color: #64748b;
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
}
.reg-error {
    color: #ef4444;
    font-size: 0.85em;
    margin-top: -15px;
    margin-bottom: 15px;
}
.reg-validation-msg {
    font-size: 0.8em;
    margin-top: 5px;
}
.reg-validation-msg.error { color: #ef4444; }
.reg-validation-msg.success { color: #10b981; }
.otp-input {
    text-align: center;
    letter-spacing: 10px;
    font-size: 1.5em;
    font-weight: 900;
}
.reg-photo-upload {
    width: 120px;
    height: 120px;
    background: #f8fafc;
    border: 2px dashed #cbd5e0;
    border-radius: 50%;
    margin: 0 auto 30px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 2em;
    cursor: pointer;
    overflow: hidden;
}
#photo-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.reg-resend {
    text-align: center;
    margin-top: 20px;
    font-size: 0.9em;
    color: #64748b;
}
.reg-resend a {
    color: var(--workedia-primary-color);
    font-weight: 700;
    text-decoration: none;
}
</style>

<script>
let currentStage = 1;
const regData = {};

function nextStage(stage) {
    if (stage === 1) {
        regData.first_name = document.getElementById('reg_first_name').value;
        regData.last_name = document.getElementById('reg_last_name').value;
        if (!regData.first_name || !regData.last_name) return alert('يرجى إدخال الاسم');
    } else if (stage === 2) {
        regData.gender = document.getElementById('reg_gender').value;
        regData.year_of_birth = document.getElementById('reg_yob').value;
        if (!regData.year_of_birth) return alert('يرجى إدخال سنة الميلاد');
    } else if (stage === 3) {
        regData.email = document.getElementById('reg_email').value;
        regData.username = document.getElementById('reg_username').value;
        if (!regData.email || !regData.username) return alert('يرجى إكمال البيانات');

        // Validation check
        validateUsernameEmail(() => {
            goToStage(4);
        });
        return;
    } else if (stage === 4) {
        regData.password = document.getElementById('reg_password').value;
        const confirm = document.getElementById('reg_password_confirm').value;
        if (!regData.password || regData.password.length < 8) return alert('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
        if (regData.password !== confirm) return alert('كلمات المرور غير متطابقة');

        sendOTP();
        return;
    }

    goToStage(stage + 1);
}

function prevStage(stage) {
    goToStage(stage - 1);
}

function goToStage(stage) {
    document.querySelectorAll('.reg-stage').forEach(el => el.classList.remove('active'));
    document.getElementById('stage-' + stage).classList.add('active');
    currentStage = stage;
}

let validationTimeout;
function debounceValidation(type) {
    clearTimeout(validationTimeout);
    validationTimeout = setTimeout(() => {
        const val = document.getElementById('reg_' + type).value;
        if (!val) {
            document.getElementById(type + '-validation-msg').innerText = '';
            return;
        }

        const fd = new FormData();
        fd.append('action', 'workedia_check_username_email');
        if (type === 'username') fd.append('username', val);
        else fd.append('email', val);

        fetch(ajaxurl, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            const msgEl = document.getElementById(type + '-validation-msg');
            if (res.success) {
                msgEl.innerText = type === 'username' ? 'اسم المستخدم متاح' : 'البريد الإلكتروني متاح';
                msgEl.className = 'reg-validation-msg success';
            } else {
                msgEl.innerText = res.data.message;
                msgEl.className = 'reg-validation-msg error';
            }
        });
    }, 500);
}

function validateUsernameEmail(callback) {
    const btn = document.getElementById('btn-stage-3');
    btn.disabled = true;
    btn.innerText = 'جاري التحقق...';

    const fd = new FormData();
    fd.append('action', 'workedia_check_username_email');
    fd.append('username', regData.username);
    fd.append('email', regData.email);

    fetch(ajaxurl, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerText = 'متابعة';
        if (res.success) {
            callback();
        } else {
            alert(res.data.message);
        }
    });
}

function sendOTP() {
    const stage4Btn = document.querySelector('#stage-4 .reg-btn');
    stage4Btn.disabled = true;
    stage4Btn.innerText = 'جاري الإرسال...';

    const fd = new FormData();
    fd.append('action', 'workedia_register_send_otp');
    fd.append('email', regData.email);

    fetch(ajaxurl, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        stage4Btn.disabled = false;
        stage4Btn.innerText = 'تأكيد وإرسال الرمز';
        if (res.success) {
            goToStage(5);
        } else {
            alert(res.data);
        }
    });
}

function verifyOTP() {
    const otp = document.getElementById('reg_otp').value;
    if (otp.length !== 6) return alert('يرجى إدخال رمز صحيح');

    const fd = new FormData();
    fd.append('action', 'workedia_register_verify_otp');
    fd.append('email', regData.email);
    fd.append('otp', otp);

    fetch(ajaxurl, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            goToStage(6);
        } else {
            alert(res.data);
        }
    });
}

function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photo-preview').innerHTML = `<img src="${e.target.result}">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function completeRegistration() {
    const btn = document.querySelector('#stage-6 .reg-btn');
    btn.disabled = true;
    btn.innerText = 'جاري المعالجة...';

    const fd = new FormData();
    fd.append('action', 'workedia_register_complete');
    for (const key in regData) {
        fd.append(key, regData[key]);
    }

    const photoInput = document.getElementById('reg_photo');
    if (photoInput.files.length > 0) {
        fd.append('profile_image', photoInput.files[0]);
    }

    fetch(ajaxurl, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            window.location.href = res.data.redirect_url;
        } else {
            btn.disabled = false;
            btn.innerText = 'إتمام التسجيل والدخول';
            alert(res.data);
        }
    });
}
</script>
