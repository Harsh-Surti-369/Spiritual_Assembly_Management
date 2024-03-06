
// Function to generate a random number between min and max (inclusive)
function getRandomNumber(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

// Fake data for devotees (you can replace it with actual data)
const devotees = [
    "Devotee 1",
    "Devotee 2",
    "Devotee 3",
    "Devotee 4",
    "Devotee 5",
];

// Function to dynamically generate devotees' names and checkboxes
function generateDevoteeCheckboxes() {
    const randomNum = getRandomNumber(5, 50); // Generate a random number between 5 and 20
    const devoteeList = document.getElementById('devoteeList');

    // Clear any existing content in the devotee list
    devoteeList.innerHTML = '';

    for (let i = 0; i < randomNum; i++) {
        const div = document.createElement('div');
        div.classList.add('devotee-item');

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'devotee';
        checkbox.value = devotees[i % devotees.length]; // Loop through the devotees array
        checkbox.id = `devotee${i}`; // Use unique IDs for each checkbox
        checkbox.classList.add('devotee-checkbox');

        const label = document.createElement('label');
        label.textContent = checkbox.value;
        label.classList.add('devotee-name'); // Apply styling to the devotee's name
        label.htmlFor = `devotee${i}`; // Associate label with corresponding checkbox

        div.appendChild(checkbox);
        div.appendChild(label);

        // Add click event listener to toggle background color on checkbox click
        div.addEventListener('click', function (event) {
            // Prevent the default behavior for clicks on the label or checkbox
            if (event.target.tagName !== 'INPUT' && event.target.tagName !== 'LABEL') {
                checkbox.checked = !checkbox.checked;
                div.classList.toggle('absent');
            }
        });

        devoteeList.appendChild(div);
    }
}

// Call the function to generate devotee checkboxes when the page loads
window.onload = generateDevoteeCheckboxes;

document.querySelector('form').addEventListener('submit', function (event) {
    event.preventDefault();
});