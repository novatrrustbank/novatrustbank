(() => {

    const csrf      = document.querySelector('meta[name="csrf-token"]').content;

    const loggedInId = window.CHAT_LOGGED_IN;
    const otherId    = window.CHAT_OTHER_ID;

    const chatBox    = document.getElementById("floatingChatBox");
    const msgBox     = document.getElementById("floatingChatMessages");
    const input      = document.getElementById("floatingChatInput");
    const fileInput  = document.getElementById("floatingChatFile");
    const sendBtn    = document.getElementById("floatingChatSend");
    const typingInd  = document.getElementById("floatingTyping");
    const onlineBadge= document.getElementById("floatingOnlineBadge");

    let lastId     = 0;
    let typingTimeout;
    let loading = false;


    /* -------------------- ROUTES -------------------- */
    const urls = {
        fetch: `/chat/widget/fetch/${otherId}?last_id=`,
        send: `/chat/widget/send`,
        typing: `/chat/typing`,
        typingCheck: `/chat/typing/status/${otherId}`,
        markRead: `/chat/mark/read`,
        online: `/chat/online/status/${otherId}`
    };


    /* -------------------------------------------------
     * RENDER MESSAGE BUBBLE
     * ------------------------------------------------- */
    function appendMessage(m) {
        const div = document.createElement("div");

        div.className = (m.sender_id == loggedInId)
            ? "msg me"
            : "msg them";

        let html = "";

        if (m.file_path) {
            if (m.file_path.match(/\.(jpg|jpeg|png|gif)$/i)) {
                html += `<img src="${m.file_path}" style="max-width:100%; border-radius:8px;">`;
            } else {
                html += `<a href="${m.file_path}" target="_blank">📎 Download File</a>`;
            }
        }

        if (m.content) {
            html += `<div>${m.content}</div>`;
        }

        div.innerHTML = html;
        msgBox.appendChild(div);

        msgBox.scrollTop = msgBox.scrollHeight;
    }


    /* -------------------------------------------------
     * FETCH MESSAGES
     * ------------------------------------------------- */
    async function fetchMessages() {

        if (loading) return;
        loading = true;

        try {
            const res = await fetch(urls.fetch + lastId);
            if (!res.ok) return;

            const data = await res.json();
            if (!Array.isArray(data) || !data.length) return;

            data.forEach(m => {
                appendMessage(m);
                lastId = m.id;
            });

            markRead();

        } catch (e) {
            console.error("Fetch error:", e);
        } finally {
            loading = false;
        }
    }


    /* -------------------------------------------------
     * SEND MESSAGE
     * ------------------------------------------------- */
    async function sendMessage() {

        const form = new FormData();
        form.append("receiver_id", otherId);
        form.append("content", input.value);

        if (fileInput.files[0]) {
            form.append("file", fileInput.files[0]);
        }

        try {
            await fetch(urls.send, {
                method: "POST",
                headers: { "X-CSRF-TOKEN": csrf },
                body: form,
                credentials: "same-origin"
            });

            input.value = "";
            fileInput.value = "";

            fetchMessages();

        } catch (e) {
            console.error("Send error:", e);
        }
    }


    sendBtn.onclick = sendMessage;

    input.addEventListener("keypress", e => {
        if (e.key === "Enter") sendMessage();
    });


    /* -------------------------------------------------
     * MARK AS READ
     * ------------------------------------------------- */
    async function markRead() {
        try {
            await fetch(urls.markRead, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrf
                },
                credentials: "same-origin",
                body: JSON.stringify({ sender_id: otherId })
            });
        } catch {}
    }



    /* -------------------------------------------------
     * SEND TYPING STATE
     * ------------------------------------------------- */
    input.addEventListener("input", () => {

        clearTimeout(typingTimeout);

        fetch(urls.typing, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrf
            },
            body: JSON.stringify({ receiver_id: otherId }),
            credentials: "same-origin"
        });

        typingTimeout = setTimeout(() => {}, 1200);

    });


    async function checkTyping() {
        try {
            const res = await fetch(urls.typingCheck);
            const data = await res.json();

            typingInd.style.display = data.typing ? "inline" : "none";

        } catch {}
    }


    /* -------------------------------------------------
     * CHECK ONLINE
     * ------------------------------------------------- */
    async function checkOnline() {
        try {
            const res = await fetch(urls.online);
            const data = await res.json();

            onlineBadge.className = data.online
                ? "badge bg-success"
                : "badge bg-secondary";

            onlineBadge.textContent = data.online ? "Online" : "Offline";

        } catch {}
    }


    /* -------------------------------------------------
     * INIT
     * ------------------------------------------------- */
    (async function init() {
        await fetchMessages();

        setInterval(fetchMessages, 1600);
        setInterval(checkTyping, 1500);
        setInterval(checkOnline, 6000);
    })();

})();
