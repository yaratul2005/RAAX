using System;
using System.Collections;
using System.Collections.Generic;
using System.ComponentModel;
using System.Linq;
using System.Runtime.CompilerServices;

namespace ErpSample.Common
{
    /// <summary>
    /// Base class for any editable record - InvoiceHeader, InvoiceLine, Customer, Item, ...
    /// Implements INotifyDataErrorInfo, which is the interface WPF uses natively to populate
    /// Validation.Errors / Validation.HasError on bound controls. No third-party validation
    /// library needed, and no XAML ValidationRule boilerplate per field.
    ///
    /// To use in a concrete model:
    ///  1. Call SetPropertyValidated (not SetProperty) in any property setter that needs rules.
    ///  2. Override ValidateProperty() and add a case per validated property.
    ///  3. Override ValidateAll() to force-check every field before Save (catches required
    ///     fields the user never touched, so tabbed-past-but-empty fields still get caught).
    /// </summary>
    public abstract class ValidatableModel : ObservableObject, INotifyDataErrorInfo
    {
        private readonly Dictionary<string, List<string>> _errors = new();

        public bool HasErrors => _errors.Count > 0;

        public event EventHandler<DataErrorsChangedEventArgs>? ErrorsChanged;

        public IEnumerable GetErrors(string? propertyName)
        {
            if (string.IsNullOrEmpty(propertyName) || !_errors.ContainsKey(propertyName))
                return Enumerable.Empty<string>();
            return _errors[propertyName];
        }

        protected void SetError(string propertyName, string message)
        {
            if (!_errors.ContainsKey(propertyName))
                _errors[propertyName] = new List<string>();

            if (!_errors[propertyName].Contains(message))
            {
                _errors[propertyName].Add(message);
                ErrorsChanged?.Invoke(this, new DataErrorsChangedEventArgs(propertyName));
                OnPropertyChanged(nameof(HasErrors));
            }
        }

        protected void ClearErrors(string propertyName)
        {
            if (_errors.Remove(propertyName))
            {
                ErrorsChanged?.Invoke(this, new DataErrorsChangedEventArgs(propertyName));
                OnPropertyChanged(nameof(HasErrors));
            }
        }

        /// <summary>
        /// Sets the backing field, raises PropertyChanged, then re-runs validation for that
        /// one property. This is what gives you "validate the moment focus leaves the field"
        /// instead of a single popup dump when the user clicks Save.
        /// </summary>
        protected bool SetPropertyValidated<T>(ref T field, T value, [CallerMemberName] string? propertyName = null)
        {
            var changed = SetProperty(ref field, value, propertyName);
            if (changed && propertyName != null)
                ValidateProperty(propertyName);
            return changed;
        }

        /// <summary>Override in the concrete model - one case per validated property.</summary>
        protected abstract void ValidateProperty(string propertyName);

        /// <summary>Force-validate every field. Call this before Save.</summary>
        public abstract bool ValidateAll();
    }
}
