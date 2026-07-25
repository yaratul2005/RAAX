using ErpSample.Common;

namespace ErpSample.Modules.Invoicing
{
    public class InvoiceLine : ValidatableModel
    {
        private string _itemName = string.Empty;
        private decimal _quantity = 1;
        private decimal _rate;

        public string ItemName
        {
            get => _itemName;
            set => SetPropertyValidated(ref _itemName, value);
        }

        public decimal Quantity
        {
            get => _quantity;
            set { if (SetPropertyValidated(ref _quantity, value)) OnPropertyChanged(nameof(Amount)); }
        }

        public decimal Rate
        {
            get => _rate;
            set { if (SetPropertyValidated(ref _rate, value)) OnPropertyChanged(nameof(Amount)); }
        }

        // Always computed, never stored - a stored Amount would drift the moment
        // someone edits Quantity or Rate directly in the grid.
        public decimal Amount => Quantity * Rate;

        protected override void ValidateProperty(string propertyName)
        {
            switch (propertyName)
            {
                case nameof(ItemName):
                    if (string.IsNullOrWhiteSpace(ItemName)) SetError(nameof(ItemName), "Item is required.");
                    else ClearErrors(nameof(ItemName));
                    break;
                case nameof(Quantity):
                    if (Quantity <= 0) SetError(nameof(Quantity), "Quantity must be greater than zero.");
                    else ClearErrors(nameof(Quantity));
                    break;
                case nameof(Rate):
                    if (Rate < 0) SetError(nameof(Rate), "Rate cannot be negative.");
                    else ClearErrors(nameof(Rate));
                    break;
            }
        }

        public override bool ValidateAll()
        {
            ValidateProperty(nameof(ItemName));
            ValidateProperty(nameof(Quantity));
            ValidateProperty(nameof(Rate));
            return !HasErrors;
        }
    }
}
