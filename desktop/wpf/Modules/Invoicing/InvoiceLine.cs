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
            get { return _itemName; }
            set { SetPropertyValidated(ref _itemName, value, "ItemName"); }
        }

        public decimal Quantity
        {
            get { return _quantity; }
            set 
            { 
                if (SetPropertyValidated(ref _quantity, value, "Quantity")) 
                    OnPropertyChanged("Amount"); 
            }
        }

        public decimal Rate
        {
            get { return _rate; }
            set 
            { 
                if (SetPropertyValidated(ref _rate, value, "Rate")) 
                    OnPropertyChanged("Amount"); 
            }
        }

        public decimal Amount
        {
            get { return Quantity * Rate; }
        }

        protected override void ValidateProperty(string propertyName)
        {
            switch (propertyName)
            {
                case "ItemName":
                    if (string.IsNullOrWhiteSpace(ItemName)) SetError("ItemName", "Item is required.");
                    else ClearErrors("ItemName");
                    break;
                case "Quantity":
                    if (Quantity <= 0) SetError("Quantity", "Quantity must be greater than zero.");
                    else ClearErrors("Quantity");
                    break;
                case "Rate":
                    if (Rate < 0) SetError("Rate", "Rate cannot be negative.");
                    else ClearErrors("Rate");
                    break;
            }
        }

        public override bool ValidateAll()
        {
            ValidateProperty("ItemName");
            ValidateProperty("Quantity");
            ValidateProperty("Rate");
            return !HasErrors;
        }
    }
}
