export function showClock() {
    const weekDays = ["日", "月", "火", "水", "木", "金", "土"];
    
    function updateTime() {
        const now = new Date();
        
        /* 日付のフォーマット (例：2024年1月1日(水)) */
        const dateOptions = {
            year: "numeric",
            month: "long",
            day: "numeric",
        };
        const formattedDate =
            now.toLocaleDateString("ja-JP", dateOptions).replace(/\u0020/g, "") + // 余分な空白を削除
            `(${weekDays[now.getDay()]})`;
        
        /* 時刻のフォーマット (例：08:00) */
        const timeOptions = {
            hour: "2-digit",
            minute: "2-digit",
            hour12: false,
        };
        const formattedTime = now.toLocaleTimeString("ja-JP", timeOptions);
        
        /* 描画更新 */
        document.getElementById("current-date").textContent = formattedDate;
        document.getElementById("current-time").textContent = formattedTime;
    }
    updateTime();
    setInterval(updateTime, 1000);
}

document.addEventListener("DOMContentLoaded", () => {
    showClock();
});
