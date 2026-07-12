/**
 * Admin Settings tab: change password handler.
 */

function handleChangePassword(event) {
  event.preventDefault();

  const currentPassword = document.getElementById('current-password').value;
  const newPassword = document.getElementById('new-password').value;
  const confirmPassword = document.getElementById('confirm-password').value;
  const errorBox = document.getElementById('change-password-error');
  errorBox.classList.add('hidden');

  if (newPassword !== confirmPassword) {
    errorBox.textContent = 'New password and confirmation do not match.';
    errorBox.classList.remove('hidden');
    return;
  }

  let formData = new FormData();
  formData.append('csrf_token', CSRF_TOKEN);
  formData.append('current_password', currentPassword);
  formData.append('new_password', newPassword);
  formData.append('confirm_password', confirmPassword);

  fetch('api/change_password.php', {
    method: 'POST',
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === 'success') {
        document.getElementById('change-password-form').reset();
        Swal.fire(
          'Password Updated',
          'Your password has been changed successfully.',
          'success',
        );
      } else {
        errorBox.textContent = data.message || 'Failed to update password.';
        errorBox.classList.remove('hidden');
      }
    })
    .catch((err) => {
      console.error('Change password error:', err);
      errorBox.textContent = 'Could not reach the server. Please try again.';
      errorBox.classList.remove('hidden');
    });
}
