const activeClass = (e) => {
    console.log(e.target.closest("a").classList.contains("active"))
    if (e.target.closest("a").classList.contains("active")) {
        e.preventDefault();
        return true;;
    } else {
        document.querySelectorAll("a").forEach((el) => {
            if (el.classList.contains("active")) {
                el.classList.remove("active");
            }
        });
        e.target.closest("a").classList.add("active");
    }
    return false;
};

const fetchMessages = (id, e = null) => {
    convId = id;


    let messages;
    btn.disabled = false;
    message.disabled = false;

    if (e) {
        let isClecked = activeClass(e);
        chat.scrollTo(0, chat.scrollHeight);
        if (isClecked) return;
    }
    messages = document.querySelector(`ul[data-conversation="${id}"]`);
    if (!messages) {
        messages = document.createElement("ul");
        messages.setAttribute("data-conversation", id);
    }

    return axios.get(`/messages/${id}`).then((response) => {
        chat.innerHTML = "";

        response.data.forEach((el) => {
            let me;

            if (el.mine) {
                me = createMessageLeft(el);
            } else {
                me = createMessageRight(el);
            }

            messages.append(me);
            chat.append(messages);
            chat.scrollTo(0, chat.scrollHeight);

        });
        return response.data;
    });
};

const fetchUsers = () => {
    listUser.innerHTML = "";
    let urlUser = "/conversations";

    return axios
        .get(urlUser)
        .then((response) => {
            urlHub = response.headers.link.match(
                /<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/
            )[1];
            hub = new URL(urlHub);
            hub.searchParams.append("topic", `/conversations/${response.data.user}`);
            // Subscribe to updates
            const eventSource = new EventSource(hub);
            eventSource.onmessage = (event) => {
                updateConversation(JSON.parse(event.data));
            };
            //   const hubUrl = response.headers.get('Link').match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1];

            response.data.conversations.forEach((el) => {
                // userCard.js
                let li = createUserCard(el.email, el.conversationId, el.content);

                li.addEventListener("click", (e) => {
                    fetchMessages(el.conversationId, e);
                });
                listUser.append(li);
                updateStateForFirstTime(el);
            });

            return response.data;
        })
        .then((data) => {
            hub.searchParams.append("topic", `/allUser`);
            // Subscribe to updates
            const eventSource = new EventSource(hub);
            eventSource.onmessage = (event) => {
                let online = JSON.parse(event.data);

                updateState(online);

                //fetchUsers()
            };
            return data;
        })
        .catch((err) => {
            console.log(err);
        });
};

btn.addEventListener("click", () => {
    let content = message.value;
    axios
        .post(`/messages/${convId}`, {
            content: content,
        })
        .then((response) => {
            // message.js
            let me = createMessageNow(content);
            message.value = "";
            let mess = chat.querySelector("ul");

            if (!mess) {
                mess = document.createElement("ul");
                mess.setAttribute("data-conversation", convId);
                chat.append(mess);
            }
            mess.append(me);

            let li = document.getElementById(`${convId}`);
            li.querySelector(".status").innerText = content.substr(0, 10);

            listUser.insertBefore(li, listUser.querySelectorAll("a")[0]);
            chat.scrollTo(0, chat.scrollHeight);
        });
});

const updateConversation = (el) => {
    let me;
    if (el.mine) {
        me = createMessageLeft(el);
    } else {
        me = createMessageRight(el);
    }
    if (document.querySelector(`ul[data-conversation="${el.conversation.id}"]`)) {
        document
            .querySelector(`ul[data-conversation="${el.conversation.id}"]`)
            .append(me);
    }

    let li = document.getElementById(`${el.conversation.id}`);
    if (!li) {
        li = createUserCard(el.user.email, el.conversation.id, el.content);

        li.addEventListener("click", fetchMessages.bind(null, el.conversation.id));
        listUser.append(li);
        updateStateForFirstTime(el);
    }
    li.querySelector(".status").innerText = el.content;

    listUser.insertBefore(li, listUser.querySelectorAll("a")[0]);
};

const addConv = (e) => {
    let id = e.target.id;
    if (id) {
        axios
            .post(`/conversations/add/${id}`, {
                id: id,
            })
            .then((response) => {
                let id = response.data.id;
                let user = response.data.otheruser;

                let li = createUserCard(user.email, id);

                li.addEventListener("click", fetchMessages.bind(null, id));
                listUser.append(li);
                updateStateForFirstTime(user);
            });
    }
};

const updateState = (online) => {
    let element = document.getElementById(`${online.email}`);
    clearInterval(resttime[online.email]);
    if (element) {
        element.innerText = online.online ?
            "online" :
            moment(online.lastActive).fromNow();
        if (!online.online) {
            resttime[online.email] = setInterval(() => {
                if (document.getElementById(`${online.email}`).innerText != "online") {
                    document.getElementById(`${online.email}`).innerText = moment(
                        online.lastActive
                    ).fromNow();
                }
            }, 1000);
        }
    }
};

const updateStateForFirstTime = (el) => {
    let element = document.getElementById(`${el.email}`);
    clearInterval(resttime[el.email]);
    if (element) {
        element.innerText =
            moment().subtract(2, "minutes") < moment(el.lastActivityAt) ?
            "online" :
            moment(el.lastActivityAt).fromNow();
        if (!(moment().subtract(1, "minutes") < moment(el.lastActivityAt))) {
            resttime[el.email] = setInterval(() => {
                if (document.getElementById(`${el.email}`).innerText != "online") {
                    document.getElementById(`${el.email}`).innerText = moment(
                        el.lastActivityAt
                    ).fromNow();
                }
            }, 1000);
        }
    }
};

poulateAllUsers = async() => {
    let urlUsers = "/user/allUsers";
    let allData = await axios.get(urlUsers);
    let users = allData.data.users;
    users.forEach((el) => {
        let div = createFriendCard(el.firstName, el.email, el.id);
        friends.append(div);
        div.querySelector("i").addEventListener("click", addConv);
    });
};
poulateAllUsers();

fetchUsers();