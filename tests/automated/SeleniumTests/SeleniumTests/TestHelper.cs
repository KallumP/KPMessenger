using OpenQA.Selenium;
using System;
using System.Collections.Generic;
using System.Text;

namespace SeleniumTests {
    class TestHelper {

        public static void SetText(IWebDriver browser, string elementType, string element, string value) {

            IWebElement foundElement = FindElement(browser, elementType, element);

            foundElement.SendKeys(value);
        }

        public static void ClickButton(IWebDriver browser, string elementType, string element) {

            IWebElement foundElement = FindElement(browser, elementType, element);

            foundElement.Click();

        }

        public static string GetInterText(IWebDriver browser, string elementType, string element) {

            IWebElement foundElement = FindElement(browser, elementType, element);

            return foundElement.Text;

        }

        static IWebElement FindElement(IWebDriver browser, string elementType, string element) {

            if (elementType == "id")
                return browser.FindElement(By.Id(element));
            else if (elementType == "css")
                return browser.FindElement(By.CssSelector(element));
            else
                return browser.FindElement(By.ClassName(element));
        }
    }
}
