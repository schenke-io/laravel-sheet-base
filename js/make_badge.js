
const fs = require('fs');
const { execSync } = require('child_process');

fs.readFile('tests/coverage/coverage.txt', 'utf8', (err, data) => {
    if (err) {
        console.error('Error reading file:', err);
    } else {
        extractAndCreateBadge(data);
    }
});

function extractAndCreateBadge(data) {
    const regex = /Lines:\s*([\d.]+)%/i;
    const match = data.match(regex);

    if (match) {
        const percentage = match[1];
        createBadge(percentage);
    } else {
        console.error('Percentage not found in the file');
    }
}

function createBadge(percentage) {
    execSync(`badgen --subject Coverage --status "${percentage}%" --color green > tests/coverage/coverage.svg`);
}


console.log('badge done');
