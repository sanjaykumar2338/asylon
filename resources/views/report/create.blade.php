<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Submit a Security Concern</h1>
        <p class="mt-2 text-sm text-gray-600">
            Use this form to anonymously report a security issue or concern. Only the reviewing team for your
            organization will be able to access the information you provide.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <strong class="block font-semibold">We found a few problems:</strong>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('report.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="org_id" value="Organization" />
            <select id="org_id" name="org_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">Select an organization</option>
                @foreach ($orgs as $org)
                    <option value="{{ $org->id }}" @selected(old('org_id') == $org->id)>{{ $org->name }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('org_id')" />
        </div>

        <div>
            <x-input-label for="category" value="Category" />
            <x-text-input id="category" name="category" type="text" class="mt-1 block w-full"
                value="{{ old('category') }}" maxlength="100" required />
            <x-input-error class="mt-2" :messages="$errors->get('category')" />
        </div>

        <div>
            <x-input-label for="description" value="Describe the issue" />
            <textarea id="description" name="description" rows="6" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('description')" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <x-input-label for="contact_name" value="Contact name (optional)" />
                <x-text-input id="contact_name" name="contact_name" type="text" class="mt-1 block w-full"
                    value="{{ old('contact_name') }}" maxlength="150" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_name')" />
            </div>

            <div>
                <x-input-label for="contact_email" value="Contact email (optional)" />
                <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full"
                    value="{{ old('contact_email') }}" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_email')" />
            </div>

            <div>
                <x-input-label for="contact_phone" value="Contact phone (optional)" />
                <x-text-input id="contact_phone" name="contact_phone" type="text" class="mt-1 block w-full"
                    value="{{ old('contact_phone') }}" maxlength="30" />
                <x-input-error class="mt-2" :messages="$errors->get('contact_phone')" />
            </div>

            <div class="flex items-center gap-2 pt-6">
                <input id="urgent" name="urgent" type="checkbox" value="1"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    @checked(old('urgent')) />
                <x-input-label for="urgent" value="Mark as urgent" />
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('urgent')" />
        </div>

        @php
            $oldAttachments = old('attachments', []);
            if (empty($oldAttachments)) {
                $oldAttachments = [[]];
            }
            $nextAttachmentIndex = count($oldAttachments);
        @endphp

        <div>
            <x-input-label value="Supporting files (optional)" />
            <p class="mt-2 text-sm text-gray-500">
                Attach photos, documents, or other files and include a short note for each attachment if needed.
            </p>
            <div id="attachmentsList" class="mt-4 space-y-4" data-next-index="{{ $nextAttachmentIndex }}">
                @foreach ($oldAttachments as $index => $attachment)
                    <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm attachment-item" data-attachment-index="{{ $index }}">
                        <div>
                            <label class="block text-sm font-medium text-gray-700" for="attachment-file-{{ $index }}">File</label>
                            <input id="attachment-file-{{ $index }}" name="attachments[{{ $index }}][file]" type="file"
                                accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 attachment-file-input" />
                            <x-input-error class="mt-2" :messages="$errors->get('attachments.' . $index . '.file')" />
                        </div>
                        <div class="mt-3">
                            <label class="block text-sm font-medium text-gray-700" for="attachment-comment-{{ $index }}">Comment (optional)</label>
                            <textarea id="attachment-comment-{{ $index }}" name="attachments[{{ $index }}][comment]" rows="2" maxlength="500"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 attachment-comment-input">{{ old('attachments.' . $index . '.comment') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('attachments.' . $index . '.comment')" />
                        </div>
                        <div class="mt-3 text-right">
                            <button type="button" class="inline-flex items-center text-sm font-semibold text-red-600 hover:text-red-500 remove-attachment-btn"
                                data-remove-index="{{ $index }}">
                                <i class="fas fa-times mr-1"></i> Remove attachment
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3">
                <button type="button" id="addAttachmentBtn"
                    class="inline-flex items-center rounded-md border border-indigo-500 px-3 py-1.5 text-sm font-semibold text-indigo-600 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <i class="fas fa-plus mr-1"></i> Add another attachment
                </button>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('attachments')" />
            <x-input-error class="mt-2" :messages="$errors->get('attachments.*.file')" />
            <x-input-error class="mt-2" :messages="$errors->get('attachments.*.comment')" />
            <div id="attachmentsPreview" class="mt-4 space-y-3" aria-live="polite"></div>
        </div>

        <template id="attachment-template">
            <div class="rounded-md border border-gray-200 bg-white p-4 shadow-sm attachment-item" data-attachment-index="__INDEX__">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="attachment-file-__INDEX__">File</label>
                    <input id="attachment-file-__INDEX__" name="attachments[__INDEX__][file]" type="file"
                        accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 attachment-file-input" />
                </div>
                <div class="mt-3">
                    <label class="block text-sm font-medium text-gray-700" for="attachment-comment-__INDEX__">Comment (optional)</label>
                    <textarea id="attachment-comment-__INDEX__" name="attachments[__INDEX__][comment]" rows="2" maxlength="500"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 attachment-comment-input"></textarea>
                </div>
                <div class="mt-3 text-right">
                    <button type="button" class="inline-flex items-center text-sm font-semibold text-red-600 hover:text-red-500 remove-attachment-btn"
                        data-remove-index="__INDEX__">
                        <i class="fas fa-times mr-1"></i> Remove attachment
                    </button>
                </div>
            </div>
        </template>

        <div class="rounded-md border border-gray-200 bg-gray-50 p-4">
            <h3 class="text-sm font-semibold text-gray-800">Optional voice message</h3>
            <p class="mt-1 text-xs text-gray-600">
                Record up to three minutes of audio. You can play it back before submitting.
            </p>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <button type="button" id="recordStartBtn"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Start recording
                </button>
                <button type="button" id="recordStopBtn"
                    class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    disabled>
                    Stop
                </button>
                <button type="button" id="recordClearBtn"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    disabled>
                    Remove recording
                </button>
            </div>
            <p id="recordingStatus" class="mt-3 text-sm text-gray-500"></p>
            <audio id="voicePreview" controls class="mt-4 hidden w-full rounded-lg bg-white"></audio>
            <input type="file" id="voiceRecordingInput" name="voice_recording" class="hidden" accept="audio/webm">
            <x-input-error class="mt-3" :messages="$errors->get('voice_recording')" />
            <p class="mt-3 text-xs text-gray-500">
                Your voice recording will be attached just like an uploaded file. This feature works best in the latest versions
                of Chrome, Edge, or Firefox.
            </p>
            <div class="mt-4">
                <x-input-label for="voice_comment" value="Comment for voice recording (optional)" />
                <textarea id="voice_comment" name="voice_comment" rows="2" maxlength="500"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('voice_comment') }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('voice_comment')" />
            </div>
        </div>

        <div class="rounded-md border border-gray-200 bg-gray-50 p-4 text-xs text-gray-600">
            <p>
                By submitting this report you acknowledge that the information provided is accurate to the best of your
                knowledge. Do not include passwords or other secrets unless absolutely necessary.
            </p>
        </div>

        <div class="flex justify-end">
            <x-primary-button>Submit report</x-primary-button>
        </div>
    </form>

    <script>
        (function () {
            const attachmentsList = document.getElementById('attachmentsList');
            const addAttachmentBtn = document.getElementById('addAttachmentBtn');
            const attachmentTemplate = document.getElementById('attachment-template');
            const previewWrapper = document.getElementById('attachmentsPreview');
            const voiceInput = document.getElementById('voiceRecordingInput');
            const voiceCommentInput = document.getElementById('voice_comment');
            const trackedUrls = [];

            if (!previewWrapper) {
                return;
            }

            const clearPreviews = () => {
                while (trackedUrls.length) {
                    const url = trackedUrls.pop();
                    URL.revokeObjectURL(url);
                }
                previewWrapper.innerHTML = '';
            };

            const renderMessage = message => {
                const msg = document.createElement('p');
                msg.className = 'text-sm text-gray-500';
                msg.textContent = message;
                previewWrapper.appendChild(msg);
            };

            const formatBytes = bytes => {
                if (!bytes) {
                    return '0 KB';
                }
                const units = ['bytes', 'KB', 'MB', 'GB'];
                const exponent = Math.min(Math.floor(Math.log(bytes) / Math.log(1024)), units.length - 1);
                const value = bytes / Math.pow(1024, exponent);
                return `${value.toFixed(exponent === 0 ? 0 : 1)} ${units[exponent]}`;
            };

            const createPreviewForFile = (file, label) => {
                const container = document.createElement('div');
                container.className = 'rounded-md border border-gray-200 bg-white p-4 shadow-sm';

                const heading = document.createElement('div');
                heading.className = 'flex items-center justify-between gap-3';
                const title = document.createElement('p');
                title.className = 'break-all text-sm font-medium text-gray-800';
                title.textContent = label ?? file.name;
                heading.appendChild(title);

                const size = document.createElement('span');
                size.className = 'text-xs text-gray-500';
                size.textContent = formatBytes(file.size);
                heading.appendChild(size);
                container.appendChild(heading);

                const mime = (file.type || '').toLowerCase();
                const previewBlock = document.createElement('div');
                previewBlock.className = 'mt-3';

                if (mime.startsWith('audio/')) {
                    const url = URL.createObjectURL(file);
                    trackedUrls.push(url);
                    const audio = document.createElement('audio');
                    audio.controls = true;
                    audio.className = 'w-full';
                    audio.src = url;
                    previewBlock.appendChild(audio);
                } else if (mime.startsWith('video/')) {
                    const url = URL.createObjectURL(file);
                    trackedUrls.push(url);
                    const video = document.createElement('video');
                    video.controls = true;
                    video.className = 'w-full rounded';
                    video.src = url;
                    video.style.maxHeight = '240px';
                    previewBlock.appendChild(video);
                } else if (mime.startsWith('image/')) {
                    const url = URL.createObjectURL(file);
                    trackedUrls.push(url);
                    const img = document.createElement('img');
                    img.src = url;
                    img.alt = label ?? file.name;
                    img.className = 'h-auto max-h-56 w-auto rounded border';
                    previewBlock.appendChild(img);
                } else {
                    const note = document.createElement('p');
                    note.className = 'text-xs text-gray-500';
                    note.textContent = mime ? `Selected file type: ${mime}` : 'Selected file ready to upload.';
                    previewBlock.appendChild(note);
                }

                container.appendChild(previewBlock);
                previewWrapper.appendChild(container);
            };

            const collectSelectedFiles = () => {
                const selections = [];

                if (attachmentsList) {
                    attachmentsList.querySelectorAll('.attachment-item').forEach(item => {
                        const fileInput = item.querySelector('.attachment-file-input');
                        if (fileInput?.files?.length) {
                            const file = fileInput.files[0];
                            const commentInput = item.querySelector('.attachment-comment-input');
                            const comment = commentInput?.value?.trim();
                            selections.push({ file, label: comment || file.name });
                        }
                    });
                }

                if (voiceInput?.files?.length) {
                    const file = voiceInput.files[0];
                    const comment = voiceCommentInput?.value?.trim();
                    selections.push({ file, label: comment || 'Voice recording' });
                }

                return selections;
            };

            const refreshAttachmentPreview = () => {
                clearPreviews();
                const selections = collectSelectedFiles();

                if (!selections.length) {
                    renderMessage('No attachments selected yet.');
                    return;
                }

                selections.forEach(({ file, label }) => createPreviewForFile(file, label));
            };

            const addAttachment = () => {
                if (!attachmentsList || !attachmentTemplate) {
                    return;
                }

                let nextIndex = Number(attachmentsList.dataset.nextIndex ?? attachmentsList.children.length ?? 0);
                if (!Number.isFinite(nextIndex)) {
                    nextIndex = attachmentsList.children.length;
                }

                attachmentsList.dataset.nextIndex = nextIndex + 1;

                const templateHtml = attachmentTemplate.innerHTML.replace(/__INDEX__/g, String(nextIndex));
                const container = document.createElement('div');
                container.innerHTML = templateHtml.trim();
                const fragment = document.createDocumentFragment();
                Array.from(container.children).forEach(child => fragment.appendChild(child));
                attachmentsList.appendChild(fragment);

                refreshAttachmentPreview();
            };

            if (addAttachmentBtn) {
                addAttachmentBtn.addEventListener('click', () => {
                    addAttachment();
                });
            }

            attachmentsList?.addEventListener('click', event => {
                const trigger = event.target.closest('.remove-attachment-btn');
                if (!trigger) {
                    return;
                }

                event.preventDefault();
                const item = trigger.closest('.attachment-item');
                if (item) {
                    const fileInput = item.querySelector('.attachment-file-input');
                    if (fileInput) {
                        const emptyTransfer = new DataTransfer();
                        fileInput.files = emptyTransfer.files;
                    }
                    item.remove();
                    refreshAttachmentPreview();
                }
            });

            attachmentsList?.addEventListener('change', event => {
                if (event.target.classList.contains('attachment-file-input')) {
                    refreshAttachmentPreview();
                }
            });

            attachmentsList?.addEventListener('input', event => {
                if (event.target.classList.contains('attachment-comment-input')) {
                    refreshAttachmentPreview();
                }
            });

            voiceInput?.addEventListener('change', refreshAttachmentPreview);
            voiceCommentInput?.addEventListener('input', refreshAttachmentPreview);

            window.refreshAttachmentPreview = refreshAttachmentPreview;
            refreshAttachmentPreview();
        })();

        (function () {
            const startBtn = document.getElementById('recordStartBtn');
            const stopBtn = document.getElementById('recordStopBtn');
            const clearBtn = document.getElementById('recordClearBtn');
            const statusEl = document.getElementById('recordingStatus');
            const previewEl = document.getElementById('voicePreview');
            const fileInput = document.getElementById('voiceRecordingInput');
            const voiceCommentInput = document.getElementById('voice_comment');

            if (!startBtn || !stopBtn || !clearBtn || !statusEl || !previewEl || !fileInput) {
                return;
            }

            const hasRecordingSupport = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia && window.MediaRecorder && window.DataTransfer);
            if (!hasRecordingSupport) {
                startBtn.disabled = true;
                startBtn.classList.add('cursor-not-allowed', 'opacity-60');
                statusEl.textContent = 'Voice recordings are not supported in this browser. You can still upload an audio file instead.';
                return;
            }

            let mediaRecorder;
            let recordedChunks = [];
            let activeStream;
            let objectUrl;
            let autoStopTimer;

            const resetRecording = () => {
                recordedChunks = [];
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                    objectUrl = null;
                }
                previewEl.src = '';
                previewEl.classList.add('hidden');
                statusEl.textContent = '';
                clearBtn.disabled = true;
                const emptyTransfer = new DataTransfer();
                fileInput.files = emptyTransfer.files;
                if (typeof window.refreshAttachmentPreview === 'function') {
                    window.refreshAttachmentPreview();
                }
            };

            const stopStreamTracks = () => {
                if (activeStream) {
                    activeStream.getTracks().forEach(track => track.stop());
                    activeStream = null;
                }
                if (autoStopTimer) {
                    clearTimeout(autoStopTimer);
                    autoStopTimer = null;
                }
            };

            startBtn.addEventListener('click', async () => {
                resetRecording();
                startBtn.disabled = true;
                statusEl.textContent = 'Requesting microphone access...';

                try {
                    activeStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                } catch (error) {
                    startBtn.disabled = false;
                    statusEl.textContent = 'Microphone permission was denied. Please allow access or upload an audio file.';
                    return;
                }

                recordedChunks = [];
                mediaRecorder = new MediaRecorder(activeStream, { mimeType: 'audio/webm' });

                mediaRecorder.ondataavailable = event => {
                    if (event.data && event.data.size > 0) {
                        recordedChunks.push(event.data);
                    }
                };

                mediaRecorder.onstop = () => {
                    stopStreamTracks();

                    if (!recordedChunks.length) {
                        startBtn.disabled = false;
                        stopBtn.disabled = true;
                        statusEl.textContent = 'No audio recorded. Try again.';
                        return;
                    }

                    const blob = new Blob(recordedChunks, { type: 'audio/webm' });
                    const fileName = `voice-report-${Date.now()}.webm`;
                    const file = new File([blob], fileName, { type: blob.type, lastModified: Date.now() });

                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;

                    objectUrl = URL.createObjectURL(blob);
                    previewEl.src = objectUrl;
                    previewEl.classList.remove('hidden');
                    previewEl.currentTime = 0;

                    startBtn.disabled = false;
                    stopBtn.disabled = true;
                    clearBtn.disabled = false;
                    statusEl.textContent = 'Recording captured. Use the player above to listen before submitting.';
                    if (typeof window.refreshAttachmentPreview === 'function') {
                        window.refreshAttachmentPreview();
                    }
                };

                mediaRecorder.start();
                statusEl.textContent = 'Recording... speak clearly into your microphone.';
                stopBtn.disabled = false;
                clearBtn.disabled = true;

                autoStopTimer = setTimeout(() => {
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                    }
                }, 3 * 60 * 1000); // 3 minutes
            });

            stopBtn.addEventListener('click', () => {
                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    mediaRecorder.stop();
                    stopBtn.disabled = true;
                    statusEl.textContent = 'Processing recording...';
                }
            });

            clearBtn.addEventListener('click', () => {
                if (mediaRecorder && mediaRecorder.state === 'recording') {
                    mediaRecorder.stop();
                }
                stopStreamTracks();
                resetRecording();
                startBtn.disabled = false;
                stopBtn.disabled = true;
                statusEl.textContent = 'Recording removed.';
                if (voiceCommentInput) {
                    voiceCommentInput.value = '';
                }
                if (typeof window.refreshAttachmentPreview === 'function') {
                    window.refreshAttachmentPreview();
                }
            });

            window.addEventListener('beforeunload', () => {
                stopStreamTracks();
                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                }
            });
        })();
    </script>
</x-guest-layout>
