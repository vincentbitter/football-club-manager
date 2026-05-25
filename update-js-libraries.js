const fs = require('fs');
const path = require('path');
const pkg = require('./package.json');

const libDir = path.join('public', 'js', 'lib');

const packages = {
    papaparse: ['papaparse.min.js']
};

// Ensure lib directory exists
if (!fs.existsSync(libDir))
    fs.mkdirSync(libDir, { recursive: true });

// Extract versions from package.json
const versions = {};
for (const lib of Object.keys(packages)) {
    if (!pkg.dependencies[lib]) {
        console.warn(`❌ Dependency '${lib}' is not in package.json`);
        continue;
    }
    versions[lib] = pkg.dependencies[lib].replace('^', '');
}

// Update versions.json
fs.writeFileSync(path.join(libDir, 'versions.json'), JSON.stringify(versions, null, 2));
console.log('✔ versions.json updated');

// Copy library files
for (const [lib, files] of Object.entries(packages)) {
    const targetDir = path.join(libDir, lib);
    fs.mkdirSync(targetDir, { recursive: true });

    files.forEach(file => {
        const src = path.join(__dirname, 'node_modules', lib, file);
        const dest = path.join(targetDir, path.basename(file));

        if (!fs.existsSync(src)) {
            console.error(`❌ ${lib}: ${file} not found at ${src}`);
            return;
        }

        fs.copyFileSync(src, dest);
        console.log(`✔ ${lib}: ${file} → ${dest}`);
    });
}
console.log(`✔ Library files copied to ${libDir}`);