* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #f5f5f5;
    padding: 40px 20px;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
}

h2 {
    color: #333;
    margin-bottom: 30px;
    font-size: 28px;
    font-weight: 300;
    text-align: center;
}

form {
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.input-group {
    display: flex;
    flex-direction: column;
}

.input-group.full-width {
    grid-column: 1 / -1;
}

.input-group label {
    font-size: 16px;
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
}

.input-group input,
.input-group select {
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    background-color: #f9f9f9;
    transition: all 0.3s ease;
    color: #666;
}

.input-group input:focus,
.input-group select:focus {
    outline: none;
    border-color: #8e7cc3;
    background-color: white;
    box-shadow: 0 0 0 3px rgba(142, 124, 195, 0.1);
}

.input-group input::placeholder {
    color: #999;
    font-size: 14px;
}

.input-group select {
    cursor: pointer;
}

.input-group select option {
    padding: 10px;
}

button[type="submit"] {
    grid-column: 1 / -1;
    background-color: #8e7cc3;
    color: white;
    padding: 15px 30px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

button[type="submit"]:hover {
    background-color: #7a6bb0;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(142, 124, 195, 0.3);
}

button[type="submit"]:active {
    transform: translateY(0);
}

.login-link {
    text-align: center;
    margin-top: 20px;
    color: #666;
    font-size: 14px;
}

.login-link a {
    color: #8e7cc3;
    text-decoration: none;
    font-weight: 500;
}

.login-link a:hover {
    text-decoration: underline;
}

/* Error messages styling */
ul {
    background-color: #fee;
    border: 1px solid #fcc;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
    max-width: 600px;
    width: 100%;
}

ul li {
    list-style-type: none;
    color: #c33;
    font-size: 14px;
    margin-bottom: 5px;
}

ul li:last-child {
    margin-bottom: 0;
}

/* Responsive design */
@media (max-width: 768px) {
    form {
        grid-template-columns: 1fr;
        padding: 30px 20px;
    }
    
    .input-group.full-width {
        grid-column: 1;
    }
    
    body {
        padding: 20px 10px;
    }
    
    h2 {
        font-size: 24px;
    }
}

@media (max-width: 480px) {
    form {
        padding: 20px 15px;
    }
    
    .input-group input,
    .input-group select {
        padding: 12px;
    }
    
    button[type="submit"] {
        padding: 12px 25px;
        font-size: 14px;
    }
}
