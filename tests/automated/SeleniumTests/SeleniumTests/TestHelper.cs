using NUnit.Framework;
using OpenQA.Selenium;
using OpenQA.Selenium.Chrome;
using OpenQA.Selenium.Support.UI;
using System;

namespace SeleniumTests {
    static class TestHelper {

        public static void SetText(IWebDriver browser, string elementType, string element, string value) {

            IWebElement foundElement = FindElement(browser, elementType, element);

            foundElement.SendKeys(value);
        }

        public static void ClickElement(IWebDriver browser, string elementType, string element) {

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
            else if (elementType == "xpath")
                return browser.FindElement(By.XPath(element));
            else
                return browser.FindElement(By.Name(element));
        }

        public static void Assert(string actual, string expected) {

            try {

                NUnit.Framework.Assert.IsTrue(actual == expected);
                Console.ForegroundColor = ConsoleColor.Green;
                Console.WriteLine("Pass");
            } catch {
                Console.ForegroundColor = ConsoleColor.Red;
                Console.WriteLine("Failed");
            } finally {
                Console.ForegroundColor = ConsoleColor.Gray;
            }
        }

        public static void Fail() {
            Console.ForegroundColor = ConsoleColor.Red;
            Console.WriteLine("Failed");
            Console.ForegroundColor = ConsoleColor.Gray;
        }

    }
}
