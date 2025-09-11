<x-expense-category-form 
    :expense_category="$expense_category"
    :action="route('expense_categories.update', $expense_category)"
    method="POST"
    mode="edit"
/>
