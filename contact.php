<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/contact.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Fatima Store - Contact Us</title>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <header class="contact-header">
        <div class="header-container">
            <h1>Get in Touch</h1>
        </div>
        <div class="p-header-container">
            <p>Have a technical question or need support with your TechNova gear? Our expert team is ready to assist you with cutting-edge solutions.</p>
        </div>
    </header>

    <section class="contact-section">
        <div class="contact-container">

            <div class="contact-form-wrapper">
                <h2>Send us a Message</h2>
                <p class="form-subtitle">Expect a response within 24 business hours.</p>
                <?php if (isset($_SESSION['contact_success'])): ?>
                    <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                        <?= $_SESSION['contact_success'];
                        unset($_SESSION['contact_success']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['contact_error'])): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                        <?= $_SESSION['contact_error'];
                        unset($_SESSION['contact_error']); ?>
                    </div>
                <?php endif; ?>
                <form class="contact-form" method="POST" action="contact_process.php">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                placeholder="Full Name"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="Name@gmail.com"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>

                        <div class="select-wrapper">
                            <select id="subject" name="subject" required>
                                <option value="">Choose a subject</option>
                                <option value="Technical Support">Technical Support</option>
                                <option value="Order Status">Order Status</option>
                                <option value="Product Inquiry">Product Inquiry</option>
                                <option value="Warranty Claim">Warranty Claim</option>
                                <option value="Feedback">Feedback</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea
                            id="message"
                            name="message"
                            placeholder="How can we help you?"
                            required></textarea>
                    </div>

                    <button type="submit" class="btn-send">
                        SEND MESSAGE
                    </button>

                </form>
            </div>

            <div class="contact-info-wrapper">
                <div class="info-card">
                    <h3>Contact Information</h3>
                    <hr class="info-divider">

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="info-text">
                            <strong>Headquarters</strong>
                            <p>Al-Zawaida, Gaza, Palestine</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="info-text">
                            <strong>Phone Support</strong>
                            <p>+970 59 214 1499</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="info-text">
                            <strong>Email Us</strong>
                            <p>fatoma@gmail.com</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div class="info-text">
                            <strong>Working Hours</strong>
                            <p>Sun - Thu: 8:00 AM - 6:00 PM (Gaza Time)</p>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </section>
    <?php include 'includes/footer.php'; ?>
</body>

</html>