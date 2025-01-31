// videoBrightness.js
export function adjustContentBrightness(video, content) {
    video.addEventListener('loadeddata', function() {
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        const frame = context.getImageData(0, 0, canvas.width, canvas.height);
        const length = frame.data.length;
        let totalBrightness = 0;

        for (let i = 0; i < length; i += 4) {
            const r = frame.data[i];
            const g = frame.data[i + 1];
            const b = frame.data[i + 2];
            const brightness = (r + g + b) / 3;
            totalBrightness += brightness;
        }

        const averageBrightness = totalBrightness / (length / 4);
        content.style.color = averageBrightness < 128 ? 'white' : 'black';
    });
}