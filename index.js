document.addEventListener('DOMContentLoaded', () => {
    const signupForm = document.getElementById('signup-form');
    const feedbackMessage = document.getElementById('feedback-message');

    if (signupForm) {
        signupForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            feedbackMessage.textContent = `Thank you! Subscribed ${email} with selected currency.`;
            feedbackMessage.style.color = 'green';
            signupForm.reset();
        });
    }

    const budgetForm = document.getElementById('budget-form');
    const expenseForm = document.getElementById('expense-form');
    const categorySelect = document.getElementById('expense-category');
    const customCategoryWrapper = document.getElementById('custom-category-wrapper');
    const customCategoryInput = document.getElementById('custom-category-input');
    const saveCustomTagBtn = document.getElementById('save-custom-tag');
    
    let totalBudget = 0;
    let expenses = [];
    let expenseChart = null;

    const categoryColors = {
        'Food': '#FF6384',
        'Transport': '#36A2EB',
        'Entertainment': '#FFCE56',
        'Social': '#4BC0C0',
        'Subscription': '#9966FF'
    };

    function getColorForCategory(cat) {
        if (categoryColors[cat]) return categoryColors[cat];
        let hash = 0;
        for (let i = 0; i < cat.length; i++) {
            hash = cat.charCodeAt(i) + ((hash << 5) - hash);
        }
        let color = '#';
        for (let i = 0; i < 3; i++) {
            let value = (hash >> (i * 8)) & 0xFF;
            color += ('00' + value.toString(16)).substr(-2);
        }
        categoryColors[cat] = color;
        return color;
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', (e) => {
            if (e.target.value === 'custom-option') {
                customCategoryWrapper.style.display = 'block';
                customCategoryInput.focus();
            } else {
                customCategoryWrapper.style.display = 'none';
            }
        });
    }

    if (saveCustomTagBtn) {
        saveCustomTagBtn.addEventListener('click', () => {
            const newTagName = customCategoryInput.value.trim();
            if (newTagName) {
                // Check if it already exists to avoid duplicates
                let exists = Array.from(categorySelect.options).some(opt => opt.value.toLowerCase() === newTagName.toLowerCase());
                
                if (!exists) {
                    const newOption = document.createElement('option');
                    newOption.value = newTagName;
                    newOption.textContent = newTagName;
                    // Insert before the custom option
                    categorySelect.insertBefore(newOption, categorySelect.lastElementChild);
                }

                categorySelect.value = newTagName;
                customCategoryWrapper.style.display = 'none';
                customCategoryInput.value = '';
            }
        });
    }

    if (budgetForm) {
        budgetForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const amount = parseFloat(document.getElementById('total-budget').value);
            if (!isNaN(amount)) {
                totalBudget = amount;
                updateDisplay();
                budgetForm.reset();
            }
        });
    }

    if (expenseForm) {
        expenseForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const amount = parseFloat(document.getElementById('expense-amount').value);
            const category = categorySelect.value;

            if (category === 'custom-option') {
                alert('Please enter and save your custom category tag first.');
                return;
            }
            
            if (!isNaN(amount)) {
                const totalSpentBefore = expenses.reduce((sum, exp) => sum + exp.amount, 0);
                const remainingBefore = totalBudget - totalSpentBefore;
                
                if (totalBudget > 0 && amount > remainingBefore) {
                    alert('Warning: This expense exceeds your remaining balance!');
                }

                const currentDate = new Date().toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });

                const remainingAfter = remainingBefore - amount;

                expenses.push({
                    date: currentDate,
                    category,
                    amount,
                    remaining: remainingAfter
                });

                updateDisplay();
                expenseForm.reset();
                categorySelect.value = 'Food';
                customCategoryWrapper.style.display = 'none';
            }
        });
    }

    function updateDisplay() {
        const totalSpent = expenses.reduce((sum, exp) => sum + exp.amount, 0);
        const remaining = totalBudget - totalSpent;

        const displayBudget = document.getElementById('display-budget');
        const displaySpent = document.getElementById('display-spent');
        const displayRemaining = document.getElementById('display-remaining');

        if (displayBudget) displayBudget.textContent = `£${totalBudget.toFixed(2)}`;
        if (displaySpent) displaySpent.textContent = `£${totalSpent.toFixed(2)}`;
        if (displayRemaining) displayRemaining.textContent = `£${remaining.toFixed(2)}`;

        const tableBody = document.getElementById('expense-table-body');
        if (tableBody) {
            if (expenses.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="4" style="text-align: center; color: #777;">No expenses recorded yet.</td></tr>`;
            } else {
                tableBody.innerHTML = expenses.map(exp => {
                    const badgeColor = getColorForCategory(exp.category);
                    return `
                        <tr>
                            <td>${exp.date}</td>
                            <td><span class="tag-badge" style="background-color: ${badgeColor};">${exp.category}</span></td>
                            <td>£${exp.amount.toFixed(2)}</td>
                            <td>£${exp.remaining.toFixed(2)}</td>
                        </tr>
                    `;
                }).join('');
            }
        }

        updateChart();
    }

    function updateChart() {
        const ctx = document.getElementById('expenseChart');
        if (!ctx) return;

        const categoryTotals = {};
        expenses.forEach(exp => {
            categoryTotals[exp.category] = (categoryTotals[exp.category] || 0) + exp.amount;
        });

        const labels = Object.keys(categoryTotals);
        const data = Object.values(categoryTotals);
        const backgroundColors = labels.map(cat => getColorForCategory(cat));

        if (expenseChart) {
            expenseChart.destroy();
        }

        expenseChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
