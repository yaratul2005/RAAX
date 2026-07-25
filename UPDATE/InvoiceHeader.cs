using System;
using System.Collections.ObjectModel;
using System.Linq;
using ErpSample.Common;

namespace ErpSample.Modules.Invoicing
{
    public class InvoiceHeader : ValidatableModel
    {
        private string _invoiceNumber = string.Empty;
        private string _customerName = string.Empty;
        private DateTime _invoiceDate = DateTime.Today;
        private DateTime _dueDate = DateTime.Today.AddDays(30);
        private string _status = "Pending";

        public string InvoiceNumber
        {
            get => _invoiceNumber;
            set => SetProperty(ref _invoiceNumber, value); // system-generated, nothing to validate
        }

        public string CustomerName
        {
            get => _customerName;
            set => SetPropertyValidated(ref _customerName, value);
        }

        public DateTime InvoiceDate
        {
            get => _invoiceDate;
            set => SetPropertyValidated(ref _invoiceDate, value);
        }

        public DateTime DueDate
        {
            get => _dueDate;
            set => SetPropertyValidated(ref _dueDate, value);
        }

        public string Status
        {
            get => _status;
            set => SetProperty(ref _status, value); // dropdown-bound, always a valid value
        }

        public ObservableCollection<InvoiceLine> Lines { get; } = new();

        public decimal Total => Lines.Sum(l => l.Amount);

        protected override void ValidateProperty(string propertyName)
        {
            switch (propertyName)
            {
                case nameof(CustomerName):
                    if (string.IsNullOrWhiteSpace(CustomerName)) SetError(nameof(CustomerName), "Customer is required.");
                    else ClearErrors(nameof(CustomerName));
                    break;

                // Cross-field rule: re-check it from whichever property changed last,
                // so the error clears no matter which of the two dates the user fixes.
                case nameof(InvoiceDate):
                case nameof(DueDate):
                    if (DueDate < InvoiceDate)
                        SetError(nameof(DueDate), "Due date cannot be before the invoice date.");
                    else
                        ClearErrors(nameof(DueDate));
                    break;
            }
        }

        public override bool ValidateAll()
        {
            ValidateProperty(nameof(CustomerName));
            ValidateProperty(nameof(InvoiceDate));
            ValidateProperty(nameof(DueDate));

            var linesValid = Lines.Count > 0 && Lines.All(l => l.ValidateAll());
            if (Lines.Count == 0) SetError(nameof(Lines), "Add at least one line item.");
            else ClearErrors(nameof(Lines));

            return !HasErrors && linesValid;
        }
    }
}
