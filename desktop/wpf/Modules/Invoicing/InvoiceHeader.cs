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
            get { return _invoiceNumber; }
            set { SetProperty(ref _invoiceNumber, value, "InvoiceNumber"); }
        }

        public string CustomerName
        {
            get { return _customerName; }
            set { SetPropertyValidated(ref _customerName, value, "CustomerName"); }
        }

        public DateTime InvoiceDate
        {
            get { return _invoiceDate; }
            set { SetPropertyValidated(ref _invoiceDate, value, "InvoiceDate"); }
        }

        public DateTime DueDate
        {
            get { return _dueDate; }
            set { SetPropertyValidated(ref _dueDate, value, "DueDate"); }
        }

        public string Status
        {
            get { return _status; }
            set { SetProperty(ref _status, value, "Status"); }
        }

        public ObservableCollection<InvoiceLine> Lines { get; private set; }

        public decimal Total
        {
            get { return Lines.Sum(l => l.Amount); }
        }

        public InvoiceHeader()
        {
            Lines = new ObservableCollection<InvoiceLine>();
        }

        protected override void ValidateProperty(string propertyName)
        {
            switch (propertyName)
            {
                case "CustomerName":
                    if (string.IsNullOrWhiteSpace(CustomerName)) SetError("CustomerName", "Customer is required.");
                    else ClearErrors("CustomerName");
                    break;

                case "InvoiceDate":
                case "DueDate":
                    if (DueDate < InvoiceDate)
                        SetError("DueDate", "Due date cannot be before invoice date.");
                    else
                        ClearErrors("DueDate");
                    break;
            }
        }

        public override bool ValidateAll()
        {
            ValidateProperty("CustomerName");
            ValidateProperty("InvoiceDate");
            ValidateProperty("DueDate");

            var linesValid = Lines.Count > 0 && Lines.All(l => l.ValidateAll());
            if (Lines.Count == 0) SetError("Lines", "Add at least one line item.");
            else ClearErrors("Lines");

            return !HasErrors && linesValid;
        }
    }
}
