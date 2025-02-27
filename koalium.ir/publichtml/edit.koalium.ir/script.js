document.addEventListener("DOMContentLoaded", function () {
    const devices = ["entranceCamera", "stairsCamera", "canopies", "garageDoor", "gardenLights"];
    
    devices.forEach(device => {
        document.getElementById(device).addEventListener("click", function () {
            let status = this.querySelector("span");
            if (status.innerText === "Active") {
                status.innerText = "Deactivated";
                this.classList.remove("active");
            } else {
                status.innerText = "Active";
                this.classList.add("active");
            }
        });
    });

    document.getElementById("solarToggle").addEventListener("change", function () {
        document.getElementById("solarStatus").innerText = this.checked ? "Enabled" : "Disabled";
    });

    document.getElementById("powerToggle").addEventListener("change", function () {
        document.getElementById("powerStatus").innerText = this.checked ? "Enabled" : "Disabled";
    });
});

function changeTemperature(amount) {
    let tempDisplay = document.querySelector(".temperature-display");
    let currentTemp = parseInt(tempDisplay.innerText);
    currentTemp += amount;
    tempDisplay.innerText = currentTemp + "°C";
}
