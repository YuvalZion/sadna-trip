function validateForm() {
    const dob = document.getElementById('dob').value;
    const password = document.getElementById('password').value;
    const email = document.getElementById('email').value;
    const today = new Date().toISOString().split('T')[0];
    
    // Validate Date of Birth
    if (dob >= today) {
        alert("תאריך הלידה חייב להיות קטן מהתאריך הנוכחי.");
        return false;
    }

    // Validate Password
    const passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/;
    if (!passwordPattern.test(password)) {
        alert("הסיסמה חייבת להכיל לפחות 6 תווים ולכלול לפחות אות אחת ומספר אחד.");
        return false;
    }

    // Validate Email Uniqueness (AJAX Call)
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'check_email.php', false);  // Synchronous request for simplicity
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('email=' + encodeURIComponent(email));

    if (xhr.responseText === 'exists') {
        alert("האימייל כבר קיים במערכת, הכנס אימייל אחר.");
        return false;
    }

    return true;
}
