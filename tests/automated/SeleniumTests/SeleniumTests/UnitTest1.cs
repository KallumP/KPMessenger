using NUnit.Framework;
using OpenQA.Selenium;
using OpenQA.Selenium.Chrome;
using OpenQA.Selenium.Support.UI;
using System;

namespace SeleniumTests {
    public class Tests {

        IWebDriver browser;

        [SetUp]
        public void Setup() {

            var options = new ChromeOptions();

            options.AcceptInsecureCertificates = true;

            browser = new ChromeDriver(options);
        }

        [Test]
        public void LoginCorrectCredentials() {

            browser.Navigate().GoToUrl("https://localhost");
            new WebDriverWait(browser, TimeSpan.FromSeconds(5)).Until(c => c.FindElement(By.CssSelector(".Login")));

            TestHelper.SetText(browser, "css", ".username_txt", "Test 1");
            Console.WriteLine("Correct Username Entered");
            TestHelper.SetText(browser, "css", ".password_txt", "test");
            Console.WriteLine("Correct Password Entered");
            TestHelper.ClickButton(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string redirectedURL = browser.Url;
            string expectedRedirect = "https://localhost/KPMessenger/site/index.php";

            Assert.IsTrue(redirectedURL == expectedRedirect);

            browser.Close();
        }

        [Test]
        public void LoginBadUsername() {

            browser.Navigate().GoToUrl("https://localhost");
            new WebDriverWait(browser, TimeSpan.FromSeconds(5)).Until(c => c.FindElement(By.CssSelector(".Login")));

            TestHelper.SetText(browser, "css", ".username_txt", "NotAUserName");
            Console.WriteLine("Bad username Entered");
            TestHelper.SetText(browser, "css", ".password_txt", "Wrong password");
            Console.WriteLine("Bad password entered");
            TestHelper.ClickButton(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "That username was wrong";

            Assert.IsTrue(errorMessage == expectedMessage);

            browser.Close();
        } 
        
        [Test]
        public void LoginBadPassword() {

            browser.Navigate().GoToUrl("https://localhost");
            new WebDriverWait(browser, TimeSpan.FromSeconds(5)).Until(c => c.FindElement(By.CssSelector(".Login")));

            TestHelper.SetText(browser, "css", ".username_txt", "Test 1");
            Console.WriteLine("Correct username Entered");
            TestHelper.SetText(browser, "css", ".password_txt", "Wrong password");
            Console.WriteLine("Bad password entered");
            TestHelper.ClickButton(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "That password was wrong";

            Assert.IsTrue(errorMessage == expectedMessage);

            browser.Close();
        }

        [Test]
        public void LoginCorrectUsernameDifferentUsersPassword() {

            browser.Navigate().GoToUrl("https://localhost");
            new WebDriverWait(browser, TimeSpan.FromSeconds(5)).Until(c => c.FindElement(By.CssSelector(".Login")));

            TestHelper.SetText(browser, "css", ".username_txt", "Test 1");
            Console.WriteLine("Correct username Entered");
            TestHelper.SetText(browser, "css", ".password_txt", "admin");
            Console.WriteLine("Password for user Test 2 entered");
            TestHelper.ClickButton(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "That password was wrong";

            Assert.IsTrue(errorMessage == expectedMessage);

            browser.Close();
        }

        [Test]
        public void LoginNoUsername() {

            browser.Navigate().GoToUrl("https://localhost");
            new WebDriverWait(browser, TimeSpan.FromSeconds(5)).Until(c => c.FindElement(By.CssSelector(".Login")));

            TestHelper.SetText(browser, "css", ".password_txt", "admin");
            Console.WriteLine("Password for user Test 2 entered");
            TestHelper.ClickButton(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "Please fill out both text boxes";

            Assert.IsTrue(errorMessage == expectedMessage);

            browser.Close();
        }

        [Test]
        public void LoginNoPassword() {

            browser.Navigate().GoToUrl("https://localhost");
            new WebDriverWait(browser, TimeSpan.FromSeconds(5)).Until(c => c.FindElement(By.CssSelector(".Login")));

            TestHelper.SetText(browser, "css", ".username_txt", "Test 1");
            Console.WriteLine("Correct username Entered");
            TestHelper.ClickButton(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "Please fill out both text boxes";

            Assert.IsTrue(errorMessage == expectedMessage);

            browser.Close();
        }

        [Test]
        public void LoginNoInputs
            () {
            browser.Navigate().GoToUrl("https://localhost");
            new WebDriverWait(browser, TimeSpan.FromSeconds(5)).Until(c => c.FindElement(By.CssSelector(".Login")));

            TestHelper.ClickButton(browser, "css", ".login_btn");
            Console.WriteLine("Login button clicked");

            string errorMessage = TestHelper.GetInterText(browser, "css", "h3");
            string expectedMessage = "Please fill out both text boxes";

            Assert.IsTrue(errorMessage == expectedMessage);

            browser.Close();
        }
    }
}