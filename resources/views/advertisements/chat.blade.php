@extends('layouts.app')
@section('content')
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" />
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="card-body pb-2 pt-2 px-0">
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="card chat-app">

                            <div class="chat">
                                <div class="chat-header clearfix">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="chat-about">
                                                <div class="d-flex align-items-center">
                                                    <div id="adminImage"><img src="{{ asset('images/default_user.png') }}" style="max-width: 50px;"></div>
                                                    <h5 class="m-b-0  ml-2">{{trans("lang.admin")}}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="chat-history" id="chat-box">
                                    <ul class="m-b-0">
                                    </ul>
                                </div>
                                <div class="chat-message clearfix">
                                    <div class="input-group mb-0">
                                        <!-- File input trigger -->
                                        <div class="input-group-prepend">
                                            <label for="fileInput" class="input-group-text" style="cursor: pointer;">
                                                <i class="fa fa-file-image-o"></i>
                                            </label>
                                            <input type="file" id="fileInput" accept="image/*,video/*" style="display: none;" />
                                        </div>

                                        <!-- Message input field -->
                                        <input type="text" id="messageInput" class="form-control" placeholder="Type your message..." />

                                        <!-- Send button -->
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" onclick="sendMessage()">
                                                <i class="fa fa-send"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.js"></script>
    <script>
        var id = "{{ $id }}";
        var senderId = "";
        var receiverId = 'admin';
        var database = firebase.firestore();
        var defaultUser = "{{ asset('images/default_user.png') }}"
        var advRef = database.collection('advertisements').doc(id).get().then(async function(snapshot) {
            if (snapshot.exists) {
                var advData = snapshot.data();
                var vendorId = advData.vendorId;
                await database.collection('vendors').doc(vendorId).get().then(async function(vendorSnapshot) {
                    if (vendorSnapshot.exists) {
                        var vendorData = vendorSnapshot.data();
                        senderId = vendorData.author;
                    }
                })
            }
        })
        var threadRef = database.collection('chat_admin').doc(id).collection("thread").orderBy("createdAt");
        threadRef.onSnapshot(snapshot => {
            const chatBox = document.querySelector("#chat-box ul");
            chatBox.innerHTML = '';

            snapshot.forEach(doc => {
                const data = doc.data();
               const isVendor = data.senderId !== "admin";
                let messageContent = '';
                let timestampText = '';

                if (data.createdAt && data.createdAt.toDate) {
                    const date = data.createdAt.toDate();
                    const formattedTime = date.toLocaleString('en-IN', {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true,
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                    timestampText = `<span class="message-data-time">${formattedTime}</span>`;
                }

                if (data.messageType === "text") {
                    messageContent = data.message;
                } else if (data.messageType === "image" && data.url?.url) {
                    messageContent = `<a href="${data.url.url}" target="_blank" rel="noopener"><img src="${data.url.url}" alt="Image"  style="max-width: 100px; border-radius: 8px;cursor: zoom-in;" /></a>`;
                } else if (data.messageType === "video" && data.url?.url) {
                    messageContent = `<video controls style="max-width: 150px; border-radius: 8px;">
                            <source src="${data.url.url}" type="${data.url.mime}">
                            Your browser does not support the video tag.
                          </video>`;
                }

                const messageHtml = `
        <li class="clearfix">
        <div class="message-data ${isVendor ? 'text-right' : ''}">
            ${timestampText}
        </div>
        <div class="message ${isVendor ? 'my-message float-right' : 'other-message'}">
            ${messageContent}
        </div>
    </li>
    `;

                chatBox.innerHTML += messageHtml;
            });

            // Auto-scroll to bottom
            chatBox.parentElement.scrollTop = chatBox.parentElement.scrollHeight;

        });

        function sendMessage() {
            const message = document.getElementById("messageInput").value;
            if (!message) return;

            database.collection("chat_admin").doc(id).collection("thread").add({
                id: database.collection("tmp").doc().id,
                message: message,
                senderId: senderId,
                receiverId: receiverId,
                messageType: "text",
                url: null,
                videoThumbnail: "",
                orderId: id,
                createdAt: firebase.firestore.FieldValue.serverTimestamp(),
            });

            // Update last message in main chat_admin doc

            const chatDocRef = database.collection("chat_admin").doc(id);

            chatDocRef.get().then(async (doc) => {
                const dataToSet = {
                    lastMessage: message,
                    lastSenderId: senderId,
                    createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                };

                if (!doc.exists) {
                    const advDoc = await database.collection('advertisements').doc(id).get();
                    const advData = advDoc.data();

                    const vendorDoc = await database.collection('vendors').doc(advData.vendorId).get();
                    const vendorData = vendorDoc.data();

                    Object.assign(dataToSet, {
                        chatType: "admin",
                        customerId: "admin",
                        customerName: "Admin",
                        customerProfileImage: "",
                        orderId: id,
                        restaurantId: vendorData.id || "",
                        restaurantName: vendorData.title || "",
                        restaurantProfileImage: vendorData.photo || "",
                    });
                }

                // Create or merge fields
                chatDocRef.set(dataToSet, {
                    merge: true
                });
            });

            document.getElementById("messageInput").value = '';
        }
        document.getElementById('fileInput').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const storageRef = firebase.storage().ref();
            const filePath = `chat_uploads/${Date.now()}_${file.name}`;
            const uploadTask = storageRef.child(filePath).put(file);

            uploadTask.on('state_changed', null,
                function(error) {
                    console.error("Upload failed:", error);
                },
                async function() {
                    const downloadURL = await uploadTask.snapshot.ref.getDownloadURL();
                    const mimeType = file.type;
                    const messageType = mimeType.startsWith("image") ? "image" : "video";

                    
                    const messageId = database.collection("tmp").doc().id;

                    let messageData = {
                        message: "sent a message",
                        messageType: messageType,
                        senderId: senderId,
                        receiverId: receiverId,
                        orderId: id,
                        createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                        id: messageId,
                        url: {
                            mime: mimeType,
                            url: downloadURL
                        }
                    };

                    if (messageType === "video") {
                        messageData.videoThumbnail = downloadURL;
                    }

                    // Save message
                    await database.collection("chat_admin").doc(id).collection("thread").add(messageData);

                    // Handle chat_admin doc creation or update
                    const chatDocRef = database.collection("chat_admin").doc(id);
                    const doc = await chatDocRef.get();

                    const dataToSet = {
                        lastMessage: "sent a message",
                        lastSenderId: senderId,
                        createdAt: firebase.firestore.FieldValue.serverTimestamp(),
                    };

                    if (!doc.exists) {
                        try {
                            const advDoc = await database.collection("advertisements").doc(id).get();
                            const advData = advDoc.data();

                            if (advData && advData.vendorId) {
                                const vendorDoc = await database.collection("vendors").doc(advData.vendorId).get();
                                const vendorData = vendorDoc.data();

                                if (vendorData) {
                                    Object.assign(dataToSet, {
                                        chatType: "admin",
                                        customerId: "admin",
                                        customerName: "Admin",
                                        customerProfileImage: "",
                                        orderId: id,
                                        restaurantId: vendorData.id || "",
                                        restaurantName: vendorData.title || "",
                                        restaurantProfileImage: vendorData.photo || "",
                                    });
                                } else {
                                    console.warn("Vendor data not found for vendorId:", advData.vendorId);
                                }
                            } else {
                                console.warn("Advertisement data missing or vendorId missing");
                            }
                        } catch (err) {
                            console.error("Error while fetching adv/vendor data:", err);
                        }
                    }

                    await chatDocRef.set(dataToSet, {
                        merge: true
                    });
                }
            );

        });
        document.getElementById("messageInput").addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                sendMessage();
            }
        });
    </script>
@endsection
