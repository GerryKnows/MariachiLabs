# Mariachi Labs Website

## Project Description

This repository contains the source code for the Mariachi Labs website. Mariachi Labs specializes in offering custom web application solutions for startups, enterprises, and consultancy, in addition to planning product strategies tailored to client needs.

The website serves as a showcase platform to display the company's services, which include:

- Custom software development
- Design of attractive and intuitive user interfaces (UI)
- Performance optimization (SEO and fast loading times)
- User experience (UX) improvement
- Product consulting and strategy

The site is available in English and Spanish.

## Technologies Used

### Frontend:

- **HTML5:** For the semantic structure of the content.
- **CSS3:** For styles and visual presentation, including a custom `style.css` file.
- **JavaScript:** For interactivity and dynamic functionalities.
  - **jQuery:** JavaScript library to simplify DOM manipulation and event handling.
  - **Bootstrap (v5.3.5):** Frontend framework for responsive design and pre-built components.
  - **AOS (Animate On Scroll):** Library to animate elements on scroll.
  - **SwiperJS:** Library to create touch sliders and carousels.
  - **Google Fonts:** For custom typography.
  - **Google reCAPTCHA:** For spam protection in forms.

### Backend (for the contact form):

- **PHP:** Server-side scripting language used to process the contact form.
- **PHPMailer:** PHP library for sending emails.

## Project Structure

```
/
├── css/                      # CSS stylesheets
│   └── style.css             # Custom styles
├── fonts/                    # Local font files (if applicable)
├── icons/                    # Icons used on the site
├── img/                      # Site images
├── mailer/                   # Contact form logic
│   ├── mailer.php            # PHP script to send emails
│   └── PHPMailer-master/     # PHPMailer library
├── index.html                # Main page in English
├── index-es.html             # Main page in Spanish
└── README.md                 # This file
```

## Usage

To view the website, simply open the `index.html` or `index-es.html` files in a web browser. For the contact form functionality (`mailer.php`) to work correctly, you will need a web server with PHP support (such as Apache or Nginx) and configure it to send emails.
