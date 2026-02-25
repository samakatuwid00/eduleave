<!-- <a href="#" id="contactModalToggle" class="dropdown-toggle nav-link" data-toggle="dropdown">Contact Us</a> -->

<!-- Modal Structure for Contacts -->
<div id="contactModal" class="modal">
    <div class="modal-content">
        <h4><strong>Contact Us</strong></h4>
        <p>If you have any questions or inquiries, feel free to reach out to us:</p>
        <ul class="contact-details">
            <li><i class="fa fa-envelope"></i> <strong>Email:</strong> eduleave07@gmail.com</li>
            <li><i class="fa fa-phone"></i> <strong>Mobile Number:</strong> 0981 630 0070</li>
            <li><i class="fa fa-map-marker-alt"></i> <strong>Address:</strong> Roxas Avenue, Brgy. Triangulo, Diversion Road, Naga City 4400</li>
        </ul>
        <button id="closeModal" class="close-btn">Close</button>
    </div>
</div>

<script>
// JavaScript for modal functionality
const modal = document.getElementById('contactModal');
const openButton = document.getElementById('contactModalToggle');
const closeButton = document.getElementById('closeModal');

openButton.addEventListener('click', (event) => {
    event.preventDefault();
    modal.style.display = 'block';
});

closeButton.addEventListener('click', () => {
    modal.style.display = 'none';
});

window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});
</script>


<style>
  /* Modal Background */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6); /* Darker overlay */
    padding-top: 100px;
    animation: fadeIn 0.5s ease-in-out;
}

/* Modal Content */
.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 30px;
    border-radius: 10px;
    width: 80%;
    max-width: 500px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    animation: slideIn 0.5s ease-out;
}

/* Header Style */
.modal-content h4 {
    font-family: 'Arial', sans-serif;
    font-size: 24px;
    color: #333;
    margin-bottom: 15px;
    text-align: center;
}

/* Modal Text */
.modal-content p {
    font-family: 'Arial', sans-serif;
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
    line-height: 1.6;
}

/* Contact details list */
.contact-details {
    font-family: 'Arial', sans-serif;
    color: #444;
    font-size: 16px;
    list-style-type: none;
    padding-left: 0;
}

.contact-details li {
    margin: 15px 0;
}

.contact-details li i {
    color: #3498db; /* Color for icons */
    margin-right: 10px; /* Space between icon and text */
    font-size: 20px; /* Icon size */
}

.contact-details li strong {
    color: #2C3E50;
}

/* Close Button */
.close-btn {
    display: block;
    width: 100%;
    padding: 12px 0;
    background-color: #3498db;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.close-btn:hover {
    background-color: #2980b9;
}

/* Animation Effects */
@keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

@keyframes slideIn {
    0% { transform: translateY(-30px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}

</style>
