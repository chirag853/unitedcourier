<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
<style>
/* UNIQUE BRAND VARIABLES */
:root {
    --uc-brand-blue: #2563eb;
    --uc-brand-purple: #9333ea;
    --uc-text-dark: #1a1a1a;
    --uc-text-muted: #666;
    --uc-bg-readonly: #f1f5f9;
}

/* SCOPED RESET & FONTS */
#uc-chatbot-wrapper {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    line-height: 1.5;
}

#uc-chatbot-wrapper h1,
#uc-chatbot-wrapper h2,
#uc-chatbot-wrapper h3,
#uc-chatbot-wrapper h4,
#uc-chatbot-wrapper h5,
#uc-chatbot-wrapper h6,
.uc-header-info h6,
.uc-bot-label {
    font-family: 'Outfit', sans-serif !important;
}

#uc-chatbot-wrapper p {
    text-align: left !important;
    margin-bottom: 0 !important;
}

/* UNIQUE GRADIENT ANIMATIONS */
.uc-moving-gradient-bg {
    background: linear-gradient(270deg, #2563eb, #9333ea, #2563eb);
    background-size: 200% 200%;
    animation: ucBgGradientMove 2s linear infinite;
}

@keyframes ucBgGradientMove {
    0% {
        background-position: 0% 50%;
    }

    100% {
        background-position: 200% 50%;
    }
}

/* FLOATING ACTION BUTTON */
#uc-chat-trigger {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    z-index: 9999;
    /* Very high to stay on top */
    transition: transform 0.2s ease;
    border:1px solid #fff;
}

#uc-chat-trigger:hover {
    transform: scale(1.05);
}

/* CHAT WINDOW CONTAINER */
#uc-chat-window {
    position: fixed;
    bottom: 90px;
    right: 20px;
    width: 380px;
    max-width: 90vw;
    height: 600px;
    max-height: 80vh;
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
    z-index: 9999;
    animation: ucSlideUp 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.1);
}

@keyframes ucSlideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* HEADER */
.uc-chat-header {
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: white;
}

.uc-back-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.uc-header-logo {
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.uc-header-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.uc-header-info h6 {
    margin: 0;
    font-weight: 500;
    font-size: 1.1rem;
    color: white;
}

.uc-header-info p {
    margin: 0;
    font-size: 0.75rem;
    opacity: 0.9;
    color: white;
}

/* BODY */
.uc-chat-body {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background-color: white;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.uc-bot-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--uc-brand-blue);
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.uc-message-bubble {
    max-width: 85%;
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 0.9rem;
    position: relative;
    line-height: 1.5;
}

.uc-bot-message {
    background-color: var(--uc-bg-readonly);
    color: var(--uc-text-dark);
    border-bottom-left-radius: 4px;
    align-self: flex-start;
}

.uc-user-message {
    background-color: var(--uc-brand-blue);
    color: white;
    border-bottom-right-radius: 4px;
    align-self: flex-end;
}

.uc-timestamp {
    font-size: 0.65rem;
    color: var(--uc-text-muted);
    margin-top: 6px;
    display: block;
}

/* OPTIONS */
.uc-options-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: flex-end;
}

.uc-option-btn {
    background: white;
    border: 1.5px solid var(--uc-brand-blue);
    border-radius: 12px;
    padding: 10px 20px;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--uc-brand-blue);
    cursor: pointer;
    transition: all 0.2s;
    width: fit-content;
}

.uc-option-btn:hover {
    background-color: var(--uc-brand-blue);
    color: white;
    box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
}

/* INPUT AREA */
.uc-chat-input-area {
    padding: 15px;
    background: white;
    border-top: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 10px;
}

.uc-chat-input {
    flex: 1;
    border: 1px solid #e2e8f0;
    border-radius: 25px;
    padding: 10px 18px;
    font-size: 0.9rem;
    outline: none;
    transition: border-color 0.2s;
    font-family: 'Inter', sans-serif;
}

.uc-chat-input:focus {
    border-color: var(--uc-brand-blue);
}

.uc-send-btn {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    border: none;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s;
}

.uc-send-btn:active {
    transform: scale(0.9);
}

/* SCROLLBAR */
.uc-chat-body::-webkit-scrollbar {
    width: 4px;
}

.uc-chat-body::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
</style>


<div id="uc-chatbot-wrapper">
    <!-- Chat Trigger FAB -->
    <div id="uc-chat-trigger" class="uc-moving-gradient-bg">
        <i data-lucide="message-circle" id="uc-icon-open"></i>
        <i data-lucide="x" id="uc-icon-close" style="display: none;"></i>
    </div>

    <!-- Chat Window -->
    <div id="uc-chat-window">
        <!-- Header -->
        <div class="uc-chat-header uc-moving-gradient-bg">
            <!-- <button class="uc-back-btn">
                <i data-lucide="chevron-left" size="18"></i>
            </button> -->
            <div class="uc-header-logo">
                <img src="{{ asset('website_images/fav-icon.png') }}" alt="Logo">
            </div>
            <div class="uc-header-info">
                <h6>United Worldwide Courier</h6>
                <p>Fast, Secure, Reliable Shipping</p>
            </div>
        </div>

        <!-- Body -->
        <div class="uc-chat-body" id="uc-chat-content">
            <div class="uc-bot-entry">
                <div class="uc-bot-label">United Courier Support</div>
                <div class="uc-message-bubble uc-bot-message">
                    <p>Welcome to United Courier! How can we assist you today?</p>
                    <span class="uc-timestamp">Just now</span>
                </div>
            </div>

            <div class="uc-bot-entry">
                <div class="uc-message-bubble uc-bot-message">
                    <p>Please select your preference:</p>
                    <span class="uc-timestamp">Just now</span>
                </div>
            </div>

            <!-- Options -->
            <div class="uc-options-container">
                <button class="uc-option-btn" onclick="ucHandleAction('Place a New Order')">Place a New Order</button>
                <button class="uc-option-btn" onclick="ucHandleAction('Track My Package')">Track My Package</button>
                <button class="uc-option-btn" onclick="ucHandleAction('Get a Quote')">Get a Quote</button>
            </div>
        </div>

        <!-- Input Area -->
        <div class="uc-chat-input-area">
            <input type="text" class="uc-chat-input" id="uc-user-input" placeholder="Type your message..."
                onkeypress="ucHandleKeyPress(event)">
            <button class="uc-send-btn uc-moving-gradient-bg" onclick="ucSendMessage()">
                <i data-lucide="send" size="18"></i>
            </button>
        </div>
    </div>
</div>

<!-- Scripting -->
<script>
// Initialize Lucide Icons
lucide.createIcons();

const ucTrigger = document.getElementById('uc-chat-trigger');
const ucWindowEl = document.getElementById('uc-chat-window');
const ucIconOpen = document.getElementById('uc-icon-open');
const ucIconClose = document.getElementById('uc-icon-close');
const ucChatContent = document.getElementById('uc-chat-content');
const ucUserInput = document.getElementById('uc-user-input');

let ucIsOpen = false;

// Toggle Chat Window
ucTrigger.addEventListener('click', () => {
    ucIsOpen = !ucIsOpen;
    if (ucIsOpen) {
        ucWindowEl.style.display = 'flex';
        ucIconOpen.style.display = 'none';
        ucIconClose.style.display = 'block';
        ucChatContent.scrollTop = ucChatContent.scrollHeight;
    } else {
        ucWindowEl.style.display = 'none';
        ucIconOpen.style.display = 'block';
        ucIconClose.style.display = 'none';
    }
});

function ucHandleKeyPress(e) {
    if (e.key === 'Enter') {
        ucSendMessage();
    }
}

function ucSendMessage() {
    const text = ucUserInput.value.trim();
    if (text === "") return;

    ucHandleAction(text);
    ucUserInput.value = "";
}

// Function to handle interactions
function ucHandleAction(choice) {
    // User Message
    const userMsgHtml = `
                <div class="uc-message-bubble uc-user-message" style="align-self: flex-end;">
                    <p>${choice}</p>
                    <span class="uc-timestamp" style="color: rgba(255,255,255,0.7)">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                </div>
            `;
    ucChatContent.insertAdjacentHTML('beforeend', userMsgHtml);
    ucChatContent.scrollTop = ucChatContent.scrollHeight;

    // Simple Bot Response Logic
    setTimeout(() => {
        let response = "I've received your inquiry about '" + choice +
            "'. A team member will get back to you shortly.";
        if (choice.toLowerCase().includes('track')) response =
            "Please provide your tracking number and I will find your package immediately.";

        const botMsgHtml = `
                    <div class="uc-bot-entry">
                        <div class="uc-bot-label">United Courier Support</div>
                        <div class="uc-message-bubble uc-bot-message">
                            <p>${response}</p>
                            <span class="uc-timestamp">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</span>
                        </div>
                    </div>
                `;
        ucChatContent.insertAdjacentHTML('beforeend', botMsgHtml);
        ucChatContent.scrollTop = ucChatContent.scrollHeight;
    }, 1000);
}
</script>