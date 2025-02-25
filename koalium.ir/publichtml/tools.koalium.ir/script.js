// script.js
const materialList = [
    "SS304", "SS316", "Monel", "Inconel", "Hastelloy", "Nickel", "Aluminium", 
    "Titanium", "Copper", "CS", "Teflon", "PVC", "PTFE", "FEP", "Plastic", "Graphite"
];

// Populate material dropdowns
function populateMaterials() {
    const materialDropdowns = document.querySelectorAll('select[id$="Layer"]');
    materialDropdowns.forEach(dropdown => {
        materialList.forEach(material => {
            const option = document.createElement('option');
            option.value = material;
            option.textContent = material;
            dropdown.appendChild(option);
        });
    });
}

// Collect all form data and send it to the backend
function sendData(action) {
    const form = document.getElementById('ruptureDiskForm');
    const formData = new FormData(form);
    const data = {};

    // Convert FormData to JSON
    formData.forEach((value, key) => {
        data[key] = value;
    });
    data['action'] = action;

    // Validate inputs
    if (!data.type || !data.size || !data.mainLayer || !data.subLayer || !data.sealLayer || 
        !data.burstPressure || !data.burstTemperature) {
        alert('Please fill all fields.');
        return;
    }

    // Send data to the Flask backend
    fetch('http://koalium.ir/rupturium', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        alert(`Data sent successfully: ${JSON.stringify(result)}`);
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Initialize the form
document.addEventListener('DOMContentLoaded', () => {
    populateMaterials();
});