const modal = document.getElementById('confirmModal');
const qtyInput = document.getElementById('borrow-qty');

function showModal() {
    const tech = document.getElementById('technician-name').value;
    const equipment = document.getElementById('jig-equipment').value;
    const customer = document.getElementById('customer-name').value;
    const qty = parseInt(qtyInput.value);
    const maxStock = parseInt(qtyInput.getAttribute('max') || 0);

    // Validation: REQ ang Tech, Equipment, Customer, at Qty
    if (!tech || !equipment || !customer || isNaN(qty) || qty <= 0) {
        alert("KULANG SA DETALYE:\nPakisagutan ang Technician, Equipment, Customer, at Quantity.");
        return;
    }

    if (qty > maxStock) {
        alert("Invalid quantity. Check available stock.");
        return;
    }

    modal.style.display = 'flex';
}

function hideModal() {
    modal.style.display = 'none';
}

function proceedSubmit() {
    const confirmBtn = document.querySelector('.btn-confirm');
    confirmBtn.disabled = true;
    confirmBtn.innerText = "Processing...";

    const formData = new FormData();
    formData.append('action', 'borrow');
    formData.append('item_uuid', document.getElementById('item-uuid').value); 
    formData.append('qty', qtyInput.value);
    formData.append('technician', document.getElementById('technician-name').value);
    formData.append('customer', document.getElementById('customer-name').value);
    
    // I-send ang JIG Equipment separately para ma-save sa column nito
    formData.append('equipment', document.getElementById('jig-equipment').value);
    
    // Remarks: "It is what it is" (kung ano lang tinype sa textarea)
    const remarksValue = document.getElementById('borrow-reason').value.trim();
    if (remarksValue !== "") {
        formData.append('remarks', remarksValue);
    }

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            alert("Success! Stock updated.");
            location.reload(); 
        } else {
            alert("Error: " + data.message);
            confirmBtn.disabled = false;
            confirmBtn.innerText = "Confirm";
        }
    })
    .catch(err => {
        alert("Server error.");
        confirmBtn.disabled = false;
        confirmBtn.innerText = "Confirm";
    });
}