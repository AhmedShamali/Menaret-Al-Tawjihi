function store(url, data) {
    axios.post(url, data)
        .then(function (response) {
            showMessage(response.data);
            clearForm();
            clearAndHideErrors();
        })
        .catch(function (error) {
            if (error.response.data.errors != undefined) {
                showErrorMessages(error.response.data.errors);
            } else {
                showMessage(error.response.data);
            }
        });
}

function update(url, data) {
    axios.put(url, data)
        .then(function (response) {
            showMessage(response.data);
            clearAndHideErrors();
        })
        .catch(function (error) {
            if (error.response.data.errors != undefined) {
                showErrorMessages(error.response.data.errors);
            } else {
                showMessage(error.response.data);
            }
        });
}

function destroy(url) {
    axios.delete(url)
        .then(function (response) {
            showMessage(response.data);
            location.reload();
        })
        .catch(function (error) {
            showMessage(error.response.data);
        });
}

function show(url) {
    return axios.get(url);
}

function index(url) {
    return axios.get(url);
}

function clearForm() {
    document.querySelectorAll('input').forEach(function (input) {
        input.value = '';
    });

    document.querySelectorAll('textarea').forEach(function (textarea) {
        textarea.value = '';
    });

    document.querySelectorAll('select').forEach(function (select) {
        select.selectedIndex = 0;
    });
}

function clearAndHideErrors() {
    document.querySelectorAll('.invalid-feedback').forEach(function (item) {
        item.innerHTML = '';
    });

    document.querySelectorAll('.is-invalid').forEach(function (item) {
        item.classList.remove('is-invalid');
    });
}

function showErrorMessages(errors) {
    clearAndHideErrors();

    Object.keys(errors).forEach(function (key) {
        let input = document.getElementById(key);

        if (input) {
            input.classList.add('is-invalid');
        }

        let error = document.getElementById(key + '_error');

        if (error) {
            error.innerHTML = errors[key][0];
        }
    });
}

function showMessage(data) {
    Swal.fire({
        icon: data.status ? 'success' : 'error',
        title: data.message,
        timer: 2000,
        showConfirmButton: false
    });
}

function confirmationMessage(title, text, callback) {
    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            callback();
        }
    });
}


function confirmDestroy(url, td) {
    Swal.fire({
        title: 'هل أنت متأكد من عملية الحذف؟',
        text: "لا يمكن التراجع عن عملية الحذف",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        // التحقق مما إذا كان المستخدم ضغط على "نعم"
        if (result.isConfirmed) {
            // إرسال طلب الحذف باستخدام Fetch API أو jQuery Ajax
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // إذا نجحت العملية، نقوم بحذف الصف من الجدول
                    td.closest('tr').remove();

                    Swal.fire(
                        'تم الحذف!',
                        'تم حذف العنصر بنجاح.',
                        'success'
                    );
                } else {
                    Swal.fire('خطأ!', 'حدث خطأ أثناء الحذف.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('خطأ!', 'حدث خطأ غير متوقع.', 'error');
            });
        }
    });
}
