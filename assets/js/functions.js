var ddup = {
    // (A) ATTACH DRAG-DROP FILE UPLOADER
    init : (instance) => {
        // (A1) FLAGS + CSS CLASS
        instance.target.classList.add("upwrap");
        instance.upqueue = []; // upload queue
        instance.uplock = false; // uploading in progress

        // (A2) DRAG-DROP HTML INTERFACE
        instance.target.innerHTML =
            `<div class="updrop">
                <svg class="box_icon" xmlns="http://www.w3.org/2000/svg" width="50" height="43" viewBox="0 0 50 43"><path d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z"></path></svg>
                Drop Files Here To Upload.
            </div>
       <div class="upstat"></div>`;
        instance.hzone = instance.target.querySelector(".updrop");
        instance.hstat = instance.target.querySelector(".upstat");

        // (A3) HIGHLIGHT DROP ZONE ON DRAG ENTER
        instance.hzone.ondragenter = (e) => {
            e.preventDefault();
            e.stopPropagation();
            instance.hzone.classList.add("highlight");
        };
        instance.hzone.ondragleave = (e) => {
            e.preventDefault();
            e.stopPropagation();
            instance.hzone.classList.remove("highlight");
        };

        // (A4) DROP TO UPLOAD FILE
        instance.hzone.ondragover = (e) => {
            e.preventDefault();
            e.stopPropagation();
        };
        instance.hzone.ondrop = (e) => {
            e.preventDefault();
            e.stopPropagation();
            instance.hzone.classList.remove("highlight");
            ddup.queue(instance, e.dataTransfer.files);
        };
    },

    // (B) UPLOAD QUEUE
    // * AJAX IS ASYNCHRONOUS, UPLOAD QUEUE PREVENTS SERVER FLOOD
    queue : (instance, files) => {
        // (B1) PUSH FILES INTO QUEUE + GENERATE HTML ROW
        for (let f of files) {
            f.hstat = document.createElement("div");
            f.hstat.className = "uprow";
            f.hstat.innerHTML =
                `<div class="upfile">${f.name} (${f.size} bytes)</div>
         <div class="upprog">
           <div class="upbar"></div>
           <div class="uppercent">0%</div>
         </div>`;
            f.hbar = f.hstat.querySelector(".upbar");
            f.hpercent = f.hstat.querySelector(".uppercent");
            instance.hstat.appendChild(f.hstat);
            instance.upqueue.push(f);
        }

        // (B2) UPLOAD!
        ddup.go(instance);
    },

    // (C) AJAX UPLOAD
    go : (instance) => { if (!instance.uplock && instance.upqueue.length!=0) {
        // (C1) UPLOAD STATUS UPDATE
        instance.uplock = true;

        // (C2) PLUCK OUT FIRST FILE IN QUEUE
        let thisfile = instance.upqueue[0];
        instance.upqueue.shift();

        // (C3) UPLOAD DATA
        let data = new FormData();
        data.append("upfile", thisfile);
        if (instance.data) { for (let [k, v] of Object.entries(instance.data)) {
            data.append(k, v);
        }}

        // (C4) AJAX UPLOAD
        let xhr = new XMLHttpRequest();
        xhr.open("POST", instance.action);

        // (C5) UPLOAD PROGRESS
        let percent = 0, width = 0;
        xhr.upload.onloadstart = (evt) => {
            thisfile.hbar.style.width = 0;
            thisfile.hpercent.innerHTML = "0%";
        };
        xhr.upload.onloadend = (evt) => {
            thisfile.hbar.style.width = "100%";
            thisfile.hpercent.innerHTML = "100%";
        };
        xhr.upload.onprogress = (evt) => {
            percent = Math.ceil((evt.loaded / evt.total) * 100) + "%";
            thisfile.hbar.style.width = percent;
            thisfile.hpercent.innerHTML = percent;
        };

        // (C6) UPLOAD COMPLETE
        xhr.onload = function () {
            // (C6-1) ERROR
            if (this.response!= "OK" || this.status!=200) {
                thisfile.hpercent.innerHTML = this.response;
                thisfile.hbar.style.backgroundColor = "#FF0000";
                thisfile.hpercent.innerHTML = "0%";
                instance.uplock = false;
                ddup.go(instance);
            }

            // (C6-2) NEXT BETTER PLAYER!
            else {
                thisfile.hbar.style.width = "100%";
                thisfile.hpercent.innerHTML = "100%";
                instance.uplock = false;
                ddup.go(instance);
            }
        };

        // (C7) GO!
        xhr.send(data);
    }}
};
