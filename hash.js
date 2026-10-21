const bcrypt = require('bcryptjs');

const password = 'pass123';
const hashedPassword = await bcrypt.hash(password, 10);

console.log(hashedPassword);

const isValid = await bcrypt.compare(password, hashedPassword);
console.log(isValid);
