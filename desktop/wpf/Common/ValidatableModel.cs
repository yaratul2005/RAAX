using System;
using System.Collections;
using System.Collections.Generic;
using System.ComponentModel;
using System.Linq;

namespace ErpSample.Common
{
    public abstract class ValidatableModel : ObservableObject, INotifyDataErrorInfo
    {
        private readonly Dictionary<string, List<string>> _errors = new Dictionary<string, List<string>>();

        public bool HasErrors
        {
            get { return _errors.Count > 0; }
        }

        public event EventHandler<DataErrorsChangedEventArgs> ErrorsChanged;

        public IEnumerable GetErrors(string propertyName)
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
                if (ErrorsChanged != null)
                {
                    ErrorsChanged(this, new DataErrorsChangedEventArgs(propertyName));
                }
                OnPropertyChanged("HasErrors");
            }
        }

        protected void ClearErrors(string propertyName)
        {
            if (_errors.Remove(propertyName))
            {
                if (ErrorsChanged != null)
                {
                    ErrorsChanged(this, new DataErrorsChangedEventArgs(propertyName));
                }
                OnPropertyChanged("HasErrors");
            }
        }

        protected bool SetPropertyValidated<T>(ref T field, T value, string propertyName)
        {
            var changed = SetProperty(ref field, value, propertyName);
            if (changed && propertyName != null)
                ValidateProperty(propertyName);
            return changed;
        }

        protected abstract void ValidateProperty(string propertyName);
        public abstract bool ValidateAll();
    }
}
